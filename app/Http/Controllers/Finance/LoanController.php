<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\EmployeeLoan;
use App\Models\LoanRepayment;
use App\Notifications\DocumentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LoanController extends Controller
{
    /**
     * Display list of all employee loans
     */
    public function index(Request $request)
    {
        $query = EmployeeLoan::with(['employee', 'document', 'disbursedBy'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('loan_number', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $loans = $query->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'pending_disbursement' => EmployeeLoan::pendingDisbursement()->count(),
            'active' => EmployeeLoan::active()->count(),
            'paid_off' => EmployeeLoan::status('paid_off')->count(),
            'total_outstanding' => EmployeeLoan::active()->sum('outstanding_amount'),
            'total_disbursed' => EmployeeLoan::whereNotNull('disbursement_date')->sum('loan_amount'),
        ];

        return view('finance.loans.index', compact('loans', 'stats'));
    }

    /**
     * Generate Receivables Report (PDF/View)
     */
    public function receivablesReport(Request $request)
    {
        $query = EmployeeLoan::with(['employee', 'document'])
            ->where('outstanding_amount', '>', 0); // Only showing active debts by default? Or all history?

        // If 'all' status is not requested, default to active loans (active, repaying, disbursed - but outstanding > 0 covers this roughly)
        // Let's rely on status 'paid_off' vs others
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: show all loans that are not fully paid? Or just list everything?
            // "Piutang" usually means active debts.
            $query->whereNotIn('status', ['paid_off', 'cancelled', 'rejected']);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $loans = $query->latest()->get();

        $totalReceivables = $loans->sum('outstanding_amount');

        // For now, return a view that is print-friendly
        return view('finance.loans.report', compact('loans', 'totalReceivables'));
    }

    /**
     * Display loan details
     */
    public function show(EmployeeLoan $loan)
    {
        $loan->load(['employee', 'document.attachments', 'disbursedBy', 'repayments.recordedBy']);

        return view('finance.loans.show', compact('loan'));
    }

    /**
     * Disburse a loan (mark as transferred to employee)
     */
    public function disburse(Request $request, EmployeeLoan $loan)
    {
        // Validate
        $request->validate([
            'disbursement_proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($loan->status !== 'pending_disbursement') {
            return back()->with('error', 'Pinjaman ini sudah dicairkan sebelumnya.');
        }

        // Handle proof file upload
        $proofPath = null;
        if ($request->hasFile('disbursement_proof')) {
            $proofPath = $request->file('disbursement_proof')->store('loan-proofs', 'public');
        }

        // Disburse the loan
        $loan->disburse(
            auth()->id(),
            $proofPath,
            $request->notes
        );

        // Send disbursement notification (bell icon) to the employee
        try {
            $loan->load('employee', 'document');
            $employee = $loan->employee;
            $amount = number_format($loan->loan_amount, 0, ',', '.');

            if ($employee && $loan->document) {
                $employee->notify(new DocumentNotification(
                    'disbursement',
                    $loan->document,
                    "Dana pinjaman Anda sebesar Rp {$amount} telah dicairkan ke rekening {$loan->bank_name} ({$loan->bank_account_number}). No. Pinjaman: {$loan->loan_number}",
                    route('my-loans.show', $loan)
                ));
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to send disbursement notification: ' . $e->getMessage());
        }

        return back()->with('success', 'Pinjaman berhasil dicairkan. Nomor: ' . $loan->loan_number);
    }

    /**
     * Record a repayment/installment
     */
    public function recordRepayment(Request $request, EmployeeLoan $loan)
    {
        // Sanitize currency inputs (strip thousand separators)
        $this->sanitizeMoneyFields($request, ['amount']);

        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:salary_deduction,transfer,cash',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string|max:500',
        ], [
            'amount.required' => 'Jumlah pembayaran wajib diisi.',
            'amount.min' => 'Jumlah pembayaran minimal Rp 1.000.',
            'payment_date.required' => 'Tanggal pembayaran wajib diisi.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
        ]);

        if (!in_array($loan->status, ['disbursed', 'repaying'])) {
            return back()->with('error', 'Pinjaman belum dicairkan atau sudah lunas.');
        }

        // Handle proof file upload
        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store('repayment-proofs', 'public');
        }

        // Record the repayment
        $loan->recordRepayment(
            (float) $request->amount,
            auth()->id(),
            [
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'proof_file' => $proofPath,
                'notes' => $request->notes,
            ]
        );

        $message = $loan->isPaidOff()
            ? 'Pembayaran berhasil dicatat. Pinjaman sudah LUNAS!'
            : 'Pembayaran cicilan ke-' . $loan->repayments()->count() . ' berhasil dicatat.';

        return back()->with('success', $message);
    }
}
