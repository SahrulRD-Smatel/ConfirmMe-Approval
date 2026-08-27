<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLoan;
use Illuminate\Http\Request;

class MyLoanController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->user()->hasPermissionTo('menu-my-loans'), 403, 'Anda tidak memiliki akses ke fitur ini.');
            return $next($request);
        });
    }

    public function index(Request $request)
    {
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

        $loan->load(['document.attachments', 'repayments.recordedBy', 'disbursedBy']);

        return view('loans.show', compact('loan'));
    }
}
