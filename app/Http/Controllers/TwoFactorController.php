<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TwoFactorController extends Controller
{
    /**
     * Show 2FA verification page for Direktur/Presiden approval
     */
    public function show(Request $request, Document $document)
    {
        // Verify user is a director or presiden and is current approver
        $user = auth()->user();
        $directorRoles = ['direktur_hr', 'direktur_finance', 'direktur_it', 'direktur_sales', 'direktur_operasional', 'presiden_direktur'];

        // Check if user has director role
        if (!$user->hasAnyRole($directorRoles)) {
            return redirect()->route('approvals.index')
                ->with('error', '2FA hanya diperlukan untuk Direktur.');
        }

        // Check if user is the current approver
        if ((int) $document->current_approver_id !== (int) $user->id) {
            return redirect()->route('approvals.index')
                ->with('error', 'Anda bukan approver saat ini untuk dokumen ini atau dokumen sudah di-approve.');
        }

        // Only generate and send OTP if one doesn't already exist in cache
        $cacheKey = "2fa_otp_{$user->id}_{$document->id}";
        $existingOtp = Cache::get($cacheKey);

        if (!$existingOtp) {
            // No valid OTP exists, generate and send a new one
            $otp = $this->generateAndSendOtp($user, $document);
        } else {
            // OTP still valid, don't resend
            $otp = $existingOtp;
        }

        // In debug mode (APP_DEBUG=true), pass OTP to view for testing
        $debugOtp = config('app.debug') ? $otp : null;

        return view('auth.verify-2fa', compact('document', 'debugOtp'));
    }

    /**
     * Verify OTP and proceed with approval
     */
    public function verify(Request $request, Document $document)
    {
        $validated = $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        $cacheKey = "2fa_otp_{$user->id}_{$document->id}";

        // Try database first, then fallback to cache
        $storedOtp = $this->getStoredOtp($user, $document);

        Log::info("2FA Verify: User {$user->id}, Doc {$document->id}, Input OTP: {$validated['otp']}, Stored OTP: " . ($storedOtp ?? 'NULL'));

        if (!$storedOtp || $storedOtp !== $validated['otp']) {
            Log::warning("2FA Failed: OTP mismatch or expired. User {$user->id}, Doc {$document->id}");
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kadaluarsa.']);
        }

        // Clear OTP
        $this->clearOtp($user, $document);
        Cache::forget($cacheKey);

        // Get pending approval data from session
        $pendingApproval = session()->get("pending_approval_{$document->id}");

        if ($pendingApproval) {
            // Process the approval automatically
            try {
                $workflowService = app(\App\Services\ApprovalWorkflowService::class);
                $workflowService->processApproval(
                    $document,
                    $user,
                    $pendingApproval['action'],
                    $pendingApproval['comment'] ?? null,
                    $pendingApproval['approved_amount'] ?? null
                );

                \App\Models\ApprovalLog::log(
                    $document,
                    $pendingApproval['action'],
                    "Dokumen di-{$pendingApproval['action']} oleh " . $user->name . " (dengan verifikasi 2FA)"
                );

                // Clear pending approval from session
                session()->forget("pending_approval_{$document->id}");

                $message = $pendingApproval['action'] === 'approved'
                    ? 'Dokumen berhasil disetujui!'
                    : 'Dokumen berhasil ditolak.';

                return redirect()->route('approvals.index')
                    ->with('success', $message);

            } catch (\Exception $e) {
                return redirect()->route('approvals.show', $document)
                    ->with('error', 'Gagal memproses approval: ' . $e->getMessage());
            }
        }

        // Fallback: Set session flag and redirect back (old behavior)
        session()->put("2fa_verified_{$document->id}", true);

        return redirect()->route('approvals.show', $document)
            ->with('success', 'Verifikasi berhasil! Silakan lanjutkan approval.');
    }

    /**
     * Resend OTP
     */
    public function resend(Request $request, Document $document)
    {
        $user = auth()->user();
        $this->generateAndSendOtp($user, $document);

        return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
    }

    /**
     * Generate and send OTP to user's email (non-blocking)
     */
    protected function generateAndSendOtp($user, Document $document): string
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $cacheKey = "2fa_otp_{$user->id}_{$document->id}";
        $expiresAt = Carbon::now()->addMinutes(10);

        // Store OTP in BOTH cache and database for reliability
        // Cache: fast lookup (works on local/Redis)
        // Database: reliable fallback (works on cPanel shared hosting)
        Cache::put($cacheKey, $otp, $expiresAt);

        // Store in database as backup
        DB::table('users')->where('id', $user->id)->update([
            'otp_code' => $otp,
            'otp_expires_at' => $expiresAt,
        ]);

        Log::info("2FA OTP Generated: User {$user->id}, Doc {$document->id}, OTP: {$otp}, Expires: {$expiresAt}");

        // Send email
        try {
            $previousTimeout = config('mail.mailers.smtp.timeout');
            config(['mail.mailers.smtp.timeout' => 5]);

            Mail::send('emails.otp', [
                'user' => $user,
                'otp' => $otp,
                'document' => $document,
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject(__('Kode Verifikasi OTP') . ' - ConfirmMe');
            });

            config(['mail.mailers.smtp.timeout' => $previousTimeout]);
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email: ' . $e->getMessage());
        }

        return $otp;
    }

    /**
     * Get stored OTP - tries cache first, then database fallback
     */
    protected function getStoredOtp($user, Document $document): ?string
    {
        $cacheKey = "2fa_otp_{$user->id}_{$document->id}";

        // Try cache first (fast)
        $cachedOtp = Cache::get($cacheKey);
        if ($cachedOtp) {
            Log::info("2FA: OTP found in cache for user {$user->id}");
            return $cachedOtp;
        }

        // Fallback to database (reliable on cPanel)
        $dbUser = DB::table('users')
            ->where('id', $user->id)
            ->whereNotNull('otp_code')
            ->where('otp_expires_at', '>', Carbon::now())
            ->first();

        if ($dbUser && $dbUser->otp_code) {
            Log::info("2FA: OTP found in database for user {$user->id} (cache miss)");
            return $dbUser->otp_code;
        }

        Log::warning("2FA: OTP not found in cache or database for user {$user->id}");
        return null;
    }

    /**
     * Clear OTP from both cache and database
     */
    protected function clearOtp($user, Document $document): void
    {
        DB::table('users')->where('id', $user->id)->update([
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);
    }

    /**
     * Check if 2FA is required for this approval
     */
    public static function isRequired(Document $document): bool
    {
        // Check if 2FA feature is enabled globally
        if (!feature_active('feature_2fa_otp')) {
            return false;
        }

        $user = auth()->user();
        $directorRoles = ['direktur_hr', 'direktur_finance', 'direktur_it', 'direktur_sales', 'direktur_operasional', 'presiden_direktur'];

        // Only required for directors and presiden
        if (!$user->hasAnyRole($directorRoles)) {
            return false;
        }

        // Check if already verified in this session
        if (session()->has("2fa_verified_{$document->id}")) {
            return false;
        }

        return true;
    }
}
