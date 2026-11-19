<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use App\Services\TotpService;
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
     * Show 2FA verification page for approval
     */
    public function show(Request $request, Document $document)
    {
        $user = auth()->user();

        // Check if 2FA is required or user has 2FA enabled
        if (!self::isRequired($document) && !$user->hasTwoFactorEnabled()) {
            return redirect()->route('approvals.index')
                ->with('error', __('Verifikasi 2FA tidak diperlukan untuk pengguna ini.'));
        }

        // Check if user is the current approver
        if ((int) $document->current_approver_id !== (int) $user->id) {
            return redirect()->route('approvals.index')
                ->with('error', __('Anda bukan approver saat ini untuk dokumen ini atau dokumen sudah di-approve.'));
        }

        $useTotp = $user->hasTwoFactorEnabled();
        $debugOtp = null;

        if (!$useTotp) {
            // Only generate and send Email OTP if one doesn't already exist in cache
            $cacheKey = "2fa_otp_{$user->id}_{$document->id}";
            $existingOtp = Cache::get($cacheKey);

            if (!$existingOtp) {
                $otp = $this->generateAndSendOtp($user, $document);
            } else {
                $otp = $existingOtp;
            }

            $debugOtp = config('app.debug') ? $otp : null;
        }

        return view('auth.verify-2fa', compact('document', 'debugOtp', 'useTotp'));
    }

    /**
     * Verify OTP or TOTP code and proceed with approval
     */
    public function verify(Request $request, Document $document)
    {
        $validated = $request->validate([
            'otp' => 'required|string',
        ]);

        $user = auth()->user();
        $inputCode = trim(str_replace([' ', '-'], '', $validated['otp']));
        $isValid = false;

        if ($user->hasTwoFactorEnabled()) {
            // 1. Verify via TOTP Authenticator App (RFC 6238)
            if (TotpService::verifyCode($user->two_factor_secret, $inputCode)) {
                $isValid = true;
            }

            // 2. Or verify via Recovery Code
            if (!$isValid && !empty($user->two_factor_recovery_codes) && is_array($user->two_factor_recovery_codes)) {
                $recoveryCodes = $user->two_factor_recovery_codes;
                $formattedInput = substr($inputCode, 0, 5) . '-' . substr($inputCode, 5);
                foreach ($recoveryCodes as $idx => $rCode) {
                    $cleanR = str_replace('-', '', $rCode);
                    if (hash_equals($cleanR, $inputCode) || hash_equals($rCode, $formattedInput)) {
                        $isValid = true;
                        unset($recoveryCodes[$idx]);
                        $user->update(['two_factor_recovery_codes' => array_values($recoveryCodes)]);
                        break;
                    }
                }
            }

            // 3. Fallback: Emergency Email OTP if user requested it
            if (!$isValid) {
                $storedOtp = $this->getStoredOtp($user, $document);
                if ($storedOtp && hash_equals($storedOtp, $inputCode)) {
                    $isValid = true;
                }
            }
        } else {
            // User uses Email OTP
            $storedOtp = $this->getStoredOtp($user, $document);
            if ($storedOtp && hash_equals($storedOtp, $inputCode)) {
                $isValid = true;
            }
        }

        Log::info("2FA Verify: User {$user->id}, Doc {$document->id}, Code: {$inputCode}, Valid: " . ($isValid ? 'YES' : 'NO'));

        if (!$isValid) {
            Log::warning("2FA Failed: Code mismatch or expired. User {$user->id}, Doc {$document->id}");
            return back()->withErrors(['otp' => __('Kode verifikasi tidak valid atau sudah kadaluarsa.')]);
        }

        // Clear OTP & Cache
        $this->clearOtp($user, $document);
        Cache::forget("2fa_otp_{$user->id}_{$document->id}");

        // Get pending approval data from session
        $pendingApproval = session()->get("pending_approval_{$document->id}");

        if ($pendingApproval) {
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

                session()->forget("pending_approval_{$document->id}");

                $message = $pendingApproval['action'] === 'approved'
                    ? __('Dokumen berhasil disetujui!')
                    : __('Dokumen berhasil ditolak.');

                return redirect()->route('approvals.index')->with('success', $message);
            } catch (\Exception $e) {
                return redirect()->route('approvals.show', $document)
                    ->with('error', __('Gagal memproses approval: ') . $e->getMessage());
            }
        }

        // Fallback: Set session flag and redirect back
        session()->put("2fa_verified_{$document->id}", true);

        return redirect()->route('approvals.show', $document)
            ->with('success', __('Verifikasi berhasil! Silakan lanjutkan approval.'));
    }

    /**
     * Resend OTP / Send Emergency Email OTP
     */
    public function resend(Request $request, Document $document)
    {
        $user = auth()->user();
        $this->generateAndSendOtp($user, $document);

        return back()->with('success', __('Kode verifikasi telah dikirim ke email Anda.'));
    }

    /**
     * Generate and send OTP to user's email (non-blocking)
     */
    protected function generateAndSendOtp($user, Document $document): string
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $cacheKey = "2fa_otp_{$user->id}_{$document->id}";
        $expiresAt = Carbon::now()->addMinutes(10);

        Cache::put($cacheKey, $otp, $expiresAt);

        DB::table('users')->where('id', $user->id)->update([
            'otp_code' => $otp,
            'otp_expires_at' => $expiresAt,
        ]);

        Log::info("2FA OTP Generated: User {$user->id}, Doc {$document->id}, OTP: {$otp}, Expires: {$expiresAt}");

        // Send email non-blocking with 5s timeout
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

        $cachedOtp = Cache::get($cacheKey);
        if ($cachedOtp) {
            return $cachedOtp;
        }

        $dbUser = DB::table('users')
            ->where('id', $user->id)
            ->whereNotNull('otp_code')
            ->where('otp_expires_at', '>', Carbon::now())
            ->first();

        if ($dbUser && $dbUser->otp_code) {
            return $dbUser->otp_code;
        }

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
     * Check if 2FA is required for this approval dynamically
     */
    public static function isRequired(Document $document): bool
    {
        if (!feature_active('feature_2fa_otp')) {
            return false;
        }

        $user = auth()->user();
        if (!$user) {
            return false;
        }

        if (session()->has("2fa_verified_{$document->id}")) {
            return false;
        }

        // If user has enabled 2FA Authenticator on their account, it's always required
        if ($user->hasTwoFactorEnabled()) {
            return true;
        }

        // Dynamic Executive / Director check (Zero hardcoding)
        $roles = $user->getRoleNames()->map(fn($r) => strtolower($r));
        $hasExecutiveRole = $roles->contains(function ($role) {
            return str_contains($role, 'direktur') || str_contains($role, 'presiden') || str_contains($role, 'executive');
        });

        $positionLower = strtolower($user->position ?? '');
        $hasExecutivePosition = str_contains($positionLower, 'direktur') || str_contains($positionLower, 'presiden');

        return $hasExecutiveRole || $hasExecutivePosition;
    }
}

