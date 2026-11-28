<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLoan;
use Illuminate\Http\Request;

class MyLoanController extends Controller
{
    public function index(Request $request)
    {
        // Auto-heal any zero-amount loans for current user
        EmployeeLoan::forEmployee(auth()->id())
            ->where('loan_amount', '<=', 0)
            ->whereNotNull('document_id')
            ->each(function ($l) {
                $l->syncFromDocument();
            });

        $loans = EmployeeLoan::with(['document', 'repayments'])
            ->forEmployee(auth()->id())
            ->latest()
            ->paginate(10);

        // Stats for current user
        $stats = [
            'active_loans' => EmployeeLoan::forEmployee(auth()->id())->active()->count(),
            'total_borrowed' => EmployeeLoan::forEmployee(auth()->id())->sum('loan_amount'),
            'total_outstanding' => EmployeeLoan::forEmployee(auth()->id())->active()->sum('outstanding_amount'),
            'paid_off' => EmployeeLoan::forEmployee(auth()->id())->status('paid_off')->count(),
        ];

        return view('loans.my-loans', compact('loans', 'stats'));
    }


    public function show(EmployeeLoan $loan)
    {
        // Ensure user can only view their own loans
        if ((int) $loan->employee_id !== (int) auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pinjaman ini.');
        }

        if ($loan->loan_amount <= 0 && $loan->document_id) {
            $loan->syncFromDocument();
            $loan->refresh();
        }

        $loan->load(['document.attachments', 'repayments.recordedBy', 'disbursedBy']);

        return view('loans.show', compact('loan'));
    }
}
