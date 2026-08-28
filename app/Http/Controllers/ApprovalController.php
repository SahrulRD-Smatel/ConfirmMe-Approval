<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentApproval;
use App\Models\DocumentComment;
use App\Models\ApprovalLog;
use App\Notifications\DocumentNotification;
use App\Services\ApprovalWorkflowService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    protected $workflowService;

    public function __construct(ApprovalWorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * Display approval queue for current user
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $documents = Document::where('current_approver_id', $user->id)
            ->with(['user', 'approvals'])
            ->when($request->filled('priority'), function ($q) use ($request) {
                $q->where('priority', $request->priority);
            })
            ->when($request->filled('approval_type'), function ($q) use ($request) {
                $q->where('approval_type', $request->approval_type);
            })
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('approvals.index', compact('documents'));
    }

    /**
     * Display approval history
     */
    public function history(Request $request)
    {
        $user = auth()->user();

        $approvals = DocumentApproval::where('approver_id', $user->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->with(['document.user'])
            ->when($request->filled('action'), function ($q) use ($request) {
                $q->where('status', $request->action);
            })
            ->latest('action_at')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'approved' => DocumentApproval::where('approver_id', $user->id)->where('status', 'approved')->count(),
            'rejected' => DocumentApproval::where('approver_id', $user->id)->where('status', 'rejected')->count(),
        ];

        return view('approvals.history', compact('approvals', 'stats'));
    }

    /**
     * Show document for approval review
     */
    public function show(Document $document)
    {
        $this->authorize('approve', $document);

        $document->load([
            'user',
            'approvals.approver',
            'attachments',
            'comments.user',
            'approvalLogs.user',
        ]);

        return view('approvals.show', compact('document'));
    }

    /**
     * Approve document
     */
    public function approve(Request $request, Document $document)
    {
        $this->authorize('approve', $document);

        // Sanitize currency input (strip thousand separators)
        $this->sanitizeMoneyFields($request, ['approved_amount']);

        $validated = $request->validate([
            'comment' => 'nullable|string|max:1000',
            'approved_amount' => 'nullable|numeric|min:0',
        ]);

        // Check if 2FA verification is required for Direktur
        if (TwoFactorController::isRequired($document)) {
            // Store pending approval data in session for processing after 2FA
            session()->put("pending_approval_{$document->id}", [
                'action' => 'approved',
                'comment' => $validated['comment'] ?? null,
                'approved_amount' => $validated['approved_amount'] ?? null,
            ]);

            return redirect()->route('2fa.show', $document)
                ->with('info', 'Verifikasi 2FA diperlukan untuk approval ini.');
        }

        // Process approval through workflow service
        $this->workflowService->processApproval(
            $document,
            auth()->user(),
            'approved',
            $validated['comment'] ?? null,
            $validated['approved_amount'] ?? null
        );

        ApprovalLog::log($document, 'approved', 'Dokumen disetujui oleh ' . auth()->user()->name);

        // Clear 2FA session flag after successful approval
        session()->forget("2fa_verified_{$document->id}");

        return redirect()->route('approvals.index')
            ->with('success', 'Dokumen berhasil disetujui!');
    }

    /**
     * Reject document
     */
    public function reject(Request $request, Document $document)
    {
        $this->authorize('approve', $document);

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        // Check if 2FA verification is required for Direktur
        if (TwoFactorController::isRequired($document)) {
            // Store pending rejection data in session for processing after 2FA
            session()->put("pending_approval_{$document->id}", [
                'action' => 'rejected',
                'comment' => $validated['comment'],
            ]);

            return redirect()->route('2fa.show', $document)
                ->with('info', 'Verifikasi 2FA diperlukan untuk penolakan ini.');
        }

        // Process rejection through workflow service
        $this->workflowService->processApproval(
            $document,
            auth()->user(),
            'rejected',
            $validated['comment']
        );

        ApprovalLog::log($document, 'rejected', 'Dokumen ditolak oleh ' . auth()->user()->name);

        return redirect()->route('approvals.index')
            ->with('success', 'Dokumen telah ditolak.');
    }

    /**
     * Request revision on document — return to requester for edits
     */
    public function revise(Request $request, Document $document)
    {
        $this->authorize('approve', $document);

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ], [
            'comment.required' => 'Catatan revisi wajib diisi.',
        ]);

        // Process revision through workflow service
        $this->workflowService->processApproval(
            $document,
            auth()->user(),
            'revision',
            $validated['comment']
        );

        ApprovalLog::log($document, 'revision_requested', 'Revisi diminta oleh ' . auth()->user()->name . ': ' . $validated['comment']);

        return redirect()->route('approvals.index')
            ->with('success', 'Permintaan revisi telah dikirim ke pemohon.');
    }

    /**
     * Add comment to document
     */
    public function comment(Request $request, Document $document)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
            'is_internal' => 'boolean',
        ]);

        DocumentComment::create([
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'comment' => $validated['comment'],
            'is_internal' => $validated['is_internal'] ?? false,
        ]);

        ApprovalLog::log($document, 'commented', 'Komentar ditambahkan');

        return back()->with('success', 'Komentar berhasil ditambahkan!');
    }

    /**
     * Show bulk approval page
     */
    public function bulk(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->hasPermissionTo('menu-bulk-approval'), 403, 'Anda tidak memiliki akses ke fitur ini.');

        // Get documents awaiting this user's approval
        $documents = Document::where('current_approver_id', $user->id)
            ->with(['user'])
            ->when($request->filled('approval_type'), function ($q) use ($request) {
                $q->where('approval_type', $request->approval_type);
            })
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")
            ->latest()
            ->get();

        // Group by approval type for easier bulk selection
        $groupedDocuments = $documents->groupBy('approval_type');

        return view('approvals.bulk', compact('documents', 'groupedDocuments'));
    }

    /**
     * Process bulk approval
     */
    public function bulkProcess(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->hasPermissionTo('menu-bulk-approval'), 403, 'Anda tidak memiliki akses ke fitur ini.');

        $validated = $request->validate([
            'document_ids' => 'required|array|min:1',
            'document_ids.*' => 'exists:documents,id',
            'action' => 'required|in:approve,reject',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Direktur cannot use bulk approval - must use individual approval with 2FA
        $direktorRoles = ['direktur_hr', 'direktur_finance', 'direktur_it', 'direktur_sales', 'direktur_operasional', 'presiden_direktur'];
        if ($user->hasAnyRole($direktorRoles) && $validated['action'] === 'approve') {
            return back()->with('error', 'Direktur tidak dapat menggunakan bulk approval. Silakan setujui dokumen satu per satu dengan verifikasi 2FA.');
        }

        $results = ['success' => 0, 'failed' => 0, 'reasons' => []];

        foreach ($validated['document_ids'] as $documentId) {
            $document = Document::find($documentId);

            // Verify user can approve this document
            if (!$document) {
                $results['failed']++;
                $results['reasons'][] = "Dokumen #{$documentId} tidak ditemukan";
                continue;
            }

            if ((int) $document->current_approver_id !== (int) $user->id) {
                $results['failed']++;
                $results['reasons'][] = "{$document->document_number}: Anda bukan approver saat ini";
                continue;
            }

            try {
                $action = $validated['action'] === 'approve' ? 'approved' : 'rejected';

                $this->workflowService->processApproval(
                    $document,
                    $user,
                    $action,
                    $validated['comment'] ?? null
                );

                ApprovalLog::log($document, $action, "Dokumen di-{$action} secara bulk oleh " . $user->name);

                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['reasons'][] = "{$document->document_number}: " . $e->getMessage();
            }
        }

        $actionLabel = $validated['action'] === 'approve' ? 'disetujui' : 'ditolak';

        if ($results['success'] > 0 && $results['failed'] === 0) {
            return redirect()->route('approvals.index')
                ->with('success', "{$results['success']} dokumen berhasil {$actionLabel}!");
        } elseif ($results['success'] > 0 && $results['failed'] > 0) {
            return redirect()->route('approvals.index')
                ->with('warning', "{$results['success']} dokumen berhasil {$actionLabel}, {$results['failed']} gagal.");
        } else {
            $errorMsg = 'Gagal memproses dokumen.';
            if (!empty($results['reasons'])) {
                $errorMsg .= ' Alasan: ' . implode('; ', array_slice($results['reasons'], 0, 3));
            }
            return back()->with('error', $errorMsg);
        }
    }
}
