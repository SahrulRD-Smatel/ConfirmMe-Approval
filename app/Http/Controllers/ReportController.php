<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Reports dashboard
     */
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'total' => Document::count(),
            'approved' => Document::where('status', 'approved')->count(),
            'rejected' => Document::where('status', 'rejected')->count(),
            'pending' => Document::whereIn('status', ['pending', 'supervisor_review', 'hrd_review', 'director_review', 'finance_review'])->count(),
            'this_month' => Document::whereMonth('created_at', now()->month)->count(),
            'avg_days' => 3,
        ];

        $byType = Document::selectRaw('approval_type, COUNT(*) as count')
            ->groupBy('approval_type')
            ->pluck('count', 'approval_type');

        $byDepartment = Document::selectRaw('department, COUNT(*) as count')
            ->groupBy('department')
            ->pluck('count', 'department');

        $recentQuery = Document::with('user');

        if (($user->hasRole('manager') && $user->hasRole('hrd')) || $user->hasRole('presiden_direktur')) {
            // Manager HRD & President Direktur: lihat semua dokumen
        } elseif ($user->hasRole('finance')) {
            // Finance: lihat dokumen PO/Invoice saja
            $recentQuery->whereIn('approval_type', ['po', 'invoice']);
        } elseif ($user->hasRole('supervisor') || $user->hasRole('manager') || $user->roles->pluck('name')->contains(fn($role) => str_starts_with($role, 'direktur_'))) {
            // Supervisor, Manager biasa & semua Direktur (direktur_hr, direktur_finance, dll): lihat dokumen departemen sendiri
            $recentQuery->where('department', $user->department);

        } elseif ($user->hasRole('staff')) {
            // Staff: lihat dokumen milik sendiri
            $recentQuery->where('user_id', $user->id);
        }

        $recentDocuments = $recentQuery->latest()->take(5)->get();

        return view('reports.index', compact('stats', 'byType', 'byDepartment', 'recentDocuments'));
    }

    /**
     * Document reports
     */
    public function documents(Request $request)
    {
        $query = Document::with(['user', 'approvals'])
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
        $documents = Document::with(['user', 'approvals'])
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
