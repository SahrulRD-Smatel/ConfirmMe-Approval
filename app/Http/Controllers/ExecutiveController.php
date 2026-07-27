<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\ApprovalLog;
use App\Models\PurchaseOrder;
use App\Models\Invoice;
use App\Services\ApprovalWorkflowService;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExecutiveController extends Controller
{
    protected ApprovalWorkflowService $workflowService;

    public function __construct(ApprovalWorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * Executive Dashboard - High-level strategic overview
     */
    public function dashboard()
    {
        $user = auth()->user();

        // Pending approvals for presiden (documents in presiden_review status)
        $pendingApprovals = Document::where('status', 'presiden_review')
            ->where('current_approver_id', $user->id)
            ->with(['user', 'approvals'])
            ->get();

        // Calculate stats
        $pendingTotalValue = $pendingApprovals->sum(function ($doc) {
            if ($doc->approval_type === 'loan' && isset($doc->metadata['loan_amount'])) {
                return (float) $doc->metadata['loan_amount'];
            }
            return $doc->estimated_budget ?? 0;
        });

        $stats = [
            'pending_count' => $pendingApprovals->count(),
            'pending_total_value' => $pendingTotalValue,
            'urgent_count' => $pendingApprovals->where('priority', 'urgent')->count(),
            'overdue_count' => $pendingApprovals->filter(fn($d) => $d->created_at->diffInDays(now()) > 7)->count(),
        ];

        // Monthly spending data (last 6 months)
        $monthlySpending = Document::where('status', 'approved')
            ->whereIn('approval_type', ['po', 'invoice', 'loan'])
            ->where('approved_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('MONTH(approved_at) as month'),
                DB::raw('YEAR(approved_at) as year'),
                DB::raw('SUM(approved_budget) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Division distribution
        $divisionData = Document::where('status', 'approved')
            ->whereIn('approval_type', ['po', 'invoice'])
            ->where('approved_at', '>=', now()->startOfYear())
            ->select('department', DB::raw('SUM(approved_budget) as total'))
            ->groupBy('department')
            ->get();

        // Approval type distribution
        $typeData = Document::whereIn('approval_type', ['po', 'invoice', 'loan'])
            ->where('status', 'presiden_review')
            ->select('approval_type', DB::raw('COUNT(*) as count'))
            ->groupBy('approval_type')
            ->get();

        return view('executive.dashboard', compact(
            'stats',
            'pendingApprovals',
            'monthlySpending',
            'divisionData',
            'typeData'
        ));
    }

    /**
     * Approval Queue - Documents pending presiden approval
     */
    public function approvals(Request $request)
    {
        $user = auth()->user();

        $query = Document::where('status', 'presiden_review')
            ->where('current_approver_id', $user->id)
            ->with(['user', 'approvals']);

        // Filters
        if ($request->filled('type')) {
            $query->where('approval_type', $request->type);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('division')) {
            $query->where('department', $request->division);
        }

        // Sort by priority (urgent first), then by date
        $documents = $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('executive.approvals', compact('documents'));
    }

    /**
     * Show single document for approval (read-only + action)
     */
    public function showApproval(Document $document)
    {
        $user = auth()->user();

        // Verify this document is pending presiden approval
        if ($document->status !== 'presiden_review' || (int) $document->current_approver_id !== (int) $user->id) {
            abort(403, 'Dokumen ini tidak dalam antrian approval Anda.');
        }

        // Check if 2FA is required
        $requires2FA = TwoFactorController::isRequired($document);

        // Load relationships
        $document->load([
            'user',
            'approvals.approver',
            'attachments',
            'approvalLogs.user'
        ]);

        return view('executive.approval-show', compact('document', 'requires2FA'));
    }

    /**
     * Approve document
     */
    public function approve(Request $request, Document $document)
    {
        $user = auth()->user();

        // Verify ownership
        if ((int) $document->current_approver_id !== (int) $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check 2FA
        if (TwoFactorController::isRequired($document)) {
            // Store pending approval data in session for processing after 2FA
            session()->put("pending_approval_{$document->id}", [
                'action' => 'approved',
                'comment' => $validated['comment'] ?? null,
            ]);

            return redirect()->route('2fa.show', $document)
                ->with('info', 'Verifikasi 2FA diperlukan untuk approval ini.');
        }

        $this->workflowService->processApproval($document, $user, 'approved', $validated['comment'] ?? null);

        return redirect()->route('executive.approvals')
            ->with('success', 'Dokumen berhasil disetujui.');
    }

    /**
     * Reject document
     */
    public function reject(Request $request, Document $document)
    {
        $user = auth()->user();

        // Verify ownership
        if ((int) $document->current_approver_id !== (int) $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        // Check 2FA
        if (TwoFactorController::isRequired($document)) {
            // Store pending rejection data in session for processing after 2FA
            session()->put("pending_approval_{$document->id}", [
                'action' => 'rejected',
                'comment' => $validated['comment'],
            ]);

            return redirect()->route('2fa.show', $document)
                ->with('info', 'Verifikasi 2FA diperlukan untuk penolakan ini.');
        }

        $this->workflowService->processApproval($document, $user, 'rejected', $validated['comment']);

        return redirect()->route('executive.approvals')
            ->with('success', 'Dokumen ditolak.');
    }

    /**
     * Request revision on document — return to requester for edits
     */
    public function revise(Request $request, Document $document)
    {
        $user = auth()->user();

        // Verify ownership
        if ((int) $document->current_approver_id !== (int) $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ], [
            'comment.required' => 'Catatan revisi wajib diisi.',
        ]);

        $this->workflowService->processApproval($document, $user, 'revision', $validated['comment']);

        return redirect()->route('executive.approvals')
            ->with('success', 'Permintaan revisi telah dikirim ke pemohon.');
    }

    /**
     * Company Insights - Global financial overview
     */
    public function insights(Request $request)
    {
        $year = $request->get('year', now()->year);

        // Total PO approved
        $totalPO = Document::where('approval_type', 'po')
            ->where('status', 'approved')
            ->whereYear('approved_at', $year)
            ->sum('approved_budget');

        $countPO = Document::where('approval_type', 'po')
            ->where('status', 'approved')
            ->whereYear('approved_at', $year)
            ->count();

        // Total Invoice paid
        $totalInvoice = Invoice::where('status', 'paid')
            ->whereYear('paid_at', $year)
            ->sum('total_amount');

        $countInvoice = Invoice::where('status', 'paid')
            ->whereYear('paid_at', $year)
            ->count();

        // Active loans
        $activeLoans = Document::where('approval_type', 'loan')
            ->whereIn('status', ['approved', 'completed'])
            ->whereYear('approved_at', $year)
            ->get();

        $totalLoans = $activeLoans->sum('approved_budget');

        // Average approval time
        $avgApprovalTime = Document::whereIn('status', ['approved', 'completed', 'paid'])
            ->whereYear('approved_at', $year)
            ->whereNotNull('approved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_hours')
            ->value('avg_hours');

        // Monthly breakdown
        $monthlyBreakdown = Document::whereIn('status', ['approved', 'completed', 'paid'])
            ->whereYear('approved_at', $year)
            ->select(
                DB::raw('MONTH(approved_at) as month'),
                'approval_type',
                DB::raw('SUM(approved_budget) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month', 'approval_type')
            ->orderBy('month')
            ->get();

        // Division breakdown
        $divisionBreakdown = Document::whereIn('status', ['approved', 'completed', 'paid'])
            ->whereYear('approved_at', $year)
            ->select(
                'department',
                DB::raw('SUM(approved_budget) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('department')
            ->orderByDesc('total')
            ->get();

        return view('executive.insights', compact(
            'year',
            'totalPO',
            'countPO',
            'totalInvoice',
            'countInvoice',
            'totalLoans',
            'activeLoans',
            'avgApprovalTime',
            'monthlyBreakdown',
            'divisionBreakdown'
        ));
    }

    /**
     * Audit & History - Strategic decision log (view-only)
     */
    public function audit(Request $request)
    {
        $query = ApprovalLog::whereIn('action', ['approved', 'rejected'])
            ->whereHas('document', function ($q) {
                $q->whereIn('approval_type', ['po', 'invoice', 'loan']);
            })
            ->with(['document', 'user']);

        // Filter by high-value only
        if ($request->boolean('high_value')) {
            $query->whereHas('document', function ($q) {
                $q->where('approved_budget', '>=', 100000000); // >= 100 juta
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->whereHas('document', function ($q) use ($request) {
                $q->where('approval_type', $request->type);
            });
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->to);
        }

        $logs = $query->orderByDesc('created_at')
            ->paginate(20);

        return view('executive.audit', compact('logs'));
    }
}
