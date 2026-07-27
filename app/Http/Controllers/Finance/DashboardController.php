<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\EmployeeLoan;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\Document;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Finance Dashboard - Control Center Keuangan
     */
    public function index()
    {
        $currentYear = date('Y');

        // Document Stats (Zona A)
        $documentStats = $this->getDocumentStats();

        // Budget KPIs (Zona B)
        $budgetKPIs = $this->getBudgetKPIs($currentYear);

        // Budgets for monitoring panel
        $budgets = Budget::where('fiscal_year', $currentYear)->get();

        // Chart Data
        $budgetChartData = $this->getBudgetChartData($currentYear);
        $cashFlowData = $this->getCashFlowData($currentYear);
        $distributionData = $this->getDistributionData();

        // Approval Queue
        $approvalQueue = $this->getApprovalQueue();

        // Alerts
        $alerts = $this->getAlerts($budgets);

        // Outstanding Invoices
        $outstandingInvoices = Invoice::with(['document', 'purchaseOrder'])
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->latest()
            ->take(5)
            ->get();

        $outstandingTotal = Invoice::whereNotIn('status', ['paid', 'cancelled'])
            ->selectRaw('SUM(total_amount) - SUM(paid_amount) as total')
            ->value('total') ?? 0;

        // Pending PO (issued but no invoice)
        $pendingPO = PurchaseOrder::with(['document'])
            ->where('status', 'issued')
            ->whereDoesntHave('invoices')
            ->latest()
            ->take(5)
            ->get();

        $pendingPOTotal = PurchaseOrder::where('status', 'issued')
            ->whereDoesntHave('invoices')
            ->sum('total_amount');

        // Employee Loans Stats
        $loanStats = [
            'pending_disbursement' => EmployeeLoan::pendingDisbursement()->count(),
            'active' => EmployeeLoan::active()->count(),
            'paid_off' => EmployeeLoan::status('paid_off')->count(),
            'total_outstanding' => EmployeeLoan::active()->sum('outstanding_amount'),
            'total_disbursed' => EmployeeLoan::whereIn('status', ['disbursed', 'repaying', 'paid_off'])->sum('loan_amount'),
        ];

        // Recent pending disbursement loans
        $pendingLoans = EmployeeLoan::with(['employee'])
            ->pendingDisbursement()
            ->latest()
            ->take(5)
            ->get();

        return view('finance.dashboard', compact(
            'documentStats',
            'budgetKPIs',
            'budgets',
            'budgetChartData',
            'cashFlowData',
            'distributionData',
            'approvalQueue',
            'alerts',
            'outstandingInvoices',
            'outstandingTotal',
            'pendingPO',
            'pendingPOTotal',
            'loanStats',
            'pendingLoans'
        ));
    }

    /**
     * Get document statistics
     */
    protected function getDocumentStats(): array
    {
        $baseQuery = Document::whereIn('approval_type', ['po', 'invoice']);

        return [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->whereIn('status', ['submitted', 'pending', 'in_review', 'finance_review'])->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
            'paid' => (clone $baseQuery)->where('status', 'paid')->count(),
        ];
    }

    /**
     * Get budget KPIs
     */
    protected function getBudgetKPIs(int $year): array
    {
        $budgets = Budget::where('fiscal_year', $year)->get();

        $total = $budgets->sum('total_amount');
        $committed = $budgets->sum('committed_budget');
        $used = $budgets->sum('used_amount');
        $remaining = $total - $committed - $used;

        return [
            'total' => $total,
            'committed' => $committed,
            'used' => $used,
            'remaining' => max(0, $remaining),
            'usage_percentage' => $total > 0 ? round(($used / $total) * 100, 1) : 0,
            'committed_percentage' => $total > 0 ? round(($committed / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Get budget vs actual chart data (per department)
     */
    protected function getBudgetChartData(int $year): array
    {
        $budgets = Budget::where('fiscal_year', $year)
            ->orderBy('department')
            ->get();

        return [
            'labels' => $budgets->pluck('department')->toArray(),
            'budget' => $budgets->pluck('total_amount')->map(fn($v) => (float) $v)->toArray(),
            'committed' => $budgets->pluck('committed_budget')->map(fn($v) => (float) $v)->toArray(),
            'used' => $budgets->pluck('used_amount')->map(fn($v) => (float) $v)->toArray(),
        ];
    }

    /**
     * Get cash flow forecast data (monthly trend)
     */
    protected function getCashFlowData(int $year): array
    {
        $months = [];
        $committed = [];
        $used = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = Carbon::create($year, $i, 1)->format('M');

            // Get committed (PO issued) in this month
            $committed[] = PurchaseOrder::whereYear('created_at', $year)
                ->whereMonth('created_at', $i)
                ->where('status', 'issued')
                ->sum('total_amount');

            // Get used (Invoice paid) in this month
            $used[] = Invoice::whereYear('paid_at', $year)
                ->whereMonth('paid_at', $i)
                ->where('status', 'paid')
                ->sum('total_amount');
        }

        return [
            'labels' => $months,
            'committed' => array_map(fn($v) => (float) $v, $committed),
            'used' => array_map(fn($v) => (float) $v, $used),
        ];
    }

    /**
     * Get distribution data (by department)
     */
    protected function getDistributionData(): array
    {
        $data = Invoice::where('invoices.status', 'paid')
            ->join('documents', 'invoices.document_id', '=', 'documents.id')
            ->selectRaw('documents.department, SUM(invoices.total_amount) as total')
            ->groupBy('documents.department')
            ->get();

        return [
            'labels' => $data->pluck('department')->toArray(),
            'values' => $data->pluck('total')->map(fn($v) => (float) $v)->toArray(),
        ];
    }

    /**
     * Get approval queue for Finance
     */
    protected function getApprovalQueue()
    {
        return Document::with(['user', 'currentApprover'])
            ->where('current_approver_id', auth()->id())
            ->whereIn('approval_type', ['po', 'invoice'])
            ->latest()
            ->take(10)
            ->get();
    }

    /**
     * Get alerts and high-risk items
     */
    protected function getAlerts($budgets): array
    {
        $alerts = [];

        // Low budget alerts (< 20% remaining)
        foreach ($budgets as $budget) {
            $remaining = $budget->total_amount - $budget->used_amount - $budget->committed_budget;
            $percentage = $budget->total_amount > 0 ? ($remaining / $budget->total_amount) * 100 : 0;

            if ($percentage < 20 && $percentage >= 0) {
                $alerts[] = [
                    'type' => 'warning',
                    'icon' => 'exclamation-triangle',
                    'message' => "Budget {$budget->department} tersisa " . number_format($percentage, 1) . "%",
                    'department' => $budget->department,
                ];
            }

            if ($percentage < 0) {
                $alerts[] = [
                    'type' => 'danger',
                    'icon' => 'x-circle',
                    'message' => "Budget {$budget->department} OVER BUDGET!",
                    'department' => $budget->department,
                ];
            }
        }

        // High value pending requests
        $highValueDocs = Document::where('current_approver_id', auth()->id())
            ->whereIn('approval_type', ['po', 'invoice'])
            ->whereIn('status', ['submitted', 'pending', 'in_review'])
            ->where('estimated_budget', '>', 50000000) // > 50 juta
            ->count();

        if ($highValueDocs > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'information-circle',
                'message' => "{$highValueDocs} pengajuan bernilai tinggi menunggu review",
            ];
        }

        // Overdue invoices
        $overdueCount = Invoice::whereNotIn('status', ['paid', 'cancelled'])
            ->where('due_date', '<', now())
            ->count();

        if ($overdueCount > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'clock',
                'message' => "{$overdueCount} invoice melewati jatuh tempo!",
            ];
        }

        return $alerts;
    }
}
