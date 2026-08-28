<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Helper to get properly scoped document query based on user permissions
     */
    private function getScopedQuery($user)
    {
        $query = Document::query();

        if ($user->hasPermissionTo('view-all-documents')) {
            return $query;
        }

        if ($user->hasPermissionTo('view-finance-documents')) {
            $query->where(function ($q) use ($user) {
                $q->whereIn('approval_type', ['po', 'po_internal', 'invoice'])
                  ->orWhere('user_id', $user->id)
                  ->orWhereHas('approvals', function ($q2) use ($user) {
                      $q2->where('approver_id', $user->id);
                  });
            });
            return $query;
        }

        if ($user->hasPermissionTo('view-department-documents')) {
            $query->where(function ($q) use ($user) {
                $q->where('department', $user->department)
                  ->orWhere('user_id', $user->id)
                  ->orWhereHas('approvals', function ($q2) use ($user) {
                      $q2->where('approver_id', $user->id);
                  });
            });
            return $query;
        }

        // Default fallback (e.g. staff): see own documents or documents they are assigned to approve/have approved
        $query->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereHas('approvals', function ($q2) use ($user) {
                  $q2->where('approver_id', $user->id);
              });
        });

        return $query;
    }

    /**
     * Reports dashboard
     */
    public function index()
    {
        $user = auth()->user();
        $baseQuery = $this->getScopedQuery($user);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
            'pending' => (clone $baseQuery)->whereIn('status', ['pending', 'supervisor_review', 'hrd_review', 'director_review', 'finance_review', 'revision', 'pending_requester_confirmation'])->count(),
            'this_month' => (clone $baseQuery)->whereMonth('created_at', now()->month)->count(),
            'avg_days' => 3,
        ];

        $byType = (clone $baseQuery)->selectRaw('approval_type, COUNT(*) as count')
            ->groupBy('approval_type')
            ->pluck('count', 'approval_type');

        $byDepartment = (clone $baseQuery)->selectRaw('department, COUNT(*) as count')
            ->groupBy('department')
            ->pluck('count', 'department');

        $recentDocuments = (clone $baseQuery)->with('user')->latest()->take(5)->get();

        return view('reports.index', compact('stats', 'byType', 'byDepartment', 'recentDocuments'));
    }

    /**
     * Document reports
     */
    public function documents(Request $request)
    {
        $user = auth()->user();
        $query = $this->getScopedQuery($user)->with(['user', 'approvals'])
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('approval_type'), function ($q) use ($request) {
                $q->where('approval_type', $request->approval_type);
            })
            ->when($request->filled('from_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from_date);
            })
            ->when($request->filled('to_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->to_date);
            })
            ->when($request->filled('department'), function ($q) use ($request) {
                $q->where('department', $request->department);
            });

        $documents = $query->latest()->paginate(20)->withQueryString();

        return view('reports.documents', compact('documents'));
    }

    /**
     * Finance reports
     * Fix #12: Synchronized data source with Finance Dashboard
     */
    public function finance(Request $request)
    {
        $currentYear = $request->get('year', now()->year);

        // Budget summary (from Budget model — same source as Finance Dashboard)
        $budgets = \App\Models\Budget::where('fiscal_year', $currentYear)->get();
        $budgetSummary = [
            'total' => $budgets->sum('total_amount'),
            'used' => $budgets->sum('used_amount'),
            'committed' => $budgets->sum('committed_budget'),
            'remaining' => max(0, $budgets->sum('total_amount') - $budgets->sum('used_amount') - $budgets->sum('committed_budget')),
        ];

        // Invoice stats (from Invoice model — actual payment data)
        $invoiceStats = [
            'paid_total' => \App\Models\Invoice::where('status', 'paid')
                ->whereYear('paid_at', $currentYear)
                ->sum('total_amount'),
            'outstanding_total' => \App\Models\Invoice::whereNotIn('status', ['paid', 'cancelled'])
                ->sum('total_amount') - \App\Models\Invoice::whereNotIn('status', ['paid', 'cancelled'])
                ->sum('paid_amount'),
            'overdue_count' => \App\Models\Invoice::whereNotIn('status', ['paid', 'cancelled'])
                ->where('due_date', '<', now())
                ->count(),
        ];

        // Monthly spending data (from Invoice paid_at — same as Dashboard cash flow)
        $monthlyData = \App\Models\Invoice::where('status', 'paid')
            ->whereYear('paid_at', $currentYear)
            ->selectRaw('MONTH(paid_at) as month, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Loan stats
        $loanStats = [
            'active' => \App\Models\EmployeeLoan::active()->count(),
            'total_outstanding' => \App\Models\EmployeeLoan::active()->sum('outstanding_amount'),
        ];

        // Legacy compatibility: budgetUsage for existing view
        $budgetUsage = $budgetSummary['used'];

        return view('reports.finance', compact('budgetSummary', 'invoiceStats', 'monthlyData', 'loanStats', 'budgetUsage', 'currentYear'));
    }

    /**
     * Export report
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $documents = $this->getScopedQuery($user)->with(['user', 'approvals'])
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('from_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from_date);
            })
            ->when($request->filled('to_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->to_date);
            })
            ->get();

        $pdf = Pdf::loadView('reports.export', compact('documents'));

        return $pdf->download('report-' . now()->format('Y-m-d') . '.pdf');
    }
}
