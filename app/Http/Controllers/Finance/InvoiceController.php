<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Document;
use App\Models\Budget;
use App\Models\PurchaseOrder;
use App\Traits\PdfPageCounter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvoiceController extends Controller
{
    use PdfPageCounter;
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $invoices = Invoice::with(['document', 'creator'])
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('invoice_number', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $unpaidAmount = Invoice::whereNotIn('status', ['paid'])->sum('total_amount') - Invoice::whereNotIn('status', ['paid'])->sum('paid_amount');
        $paidAmount = Invoice::where('status', 'paid')->sum('total_amount');
        $overdueCount = Invoice::where('status', '!=', 'paid')->where('due_date', '<', now())->count();

        // Get approved documents that need Invoice generation
        $pendingDocuments = Document::where('status', 'approved')
            ->where('approval_type', 'invoice')
            ->whereDoesntHave('invoice')
            ->with('user')
            ->latest()
            ->get();

        return view('finance.invoices.index', compact('invoices', 'unpaidAmount', 'paidAmount', 'overdueCount', 'pendingDocuments'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create(Request $request)
    {
        $document = null;
        if ($request->filled('document_id')) {
            $document = Document::findOrFail($request->document_id);
        }

        $documents = Document::where('approval_type', 'invoice')
            ->where('status', 'approved')
            ->get();

        return view('finance.invoices.create', compact('document', 'documents'));
    }

    /**
     * Store a newly created invoice.
     */
    public function store(Request $request)
    {
        // Sanitize currency inputs (strip thousand separators)
        $this->sanitizeMoneyFields($request, ['subtotal', 'tax_amount']);

        $validated = $request->validate([
            'document_id' => 'required|exists:documents,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'vendor_name' => 'required|string|max:255',
            'vendor_address' => 'nullable|string',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $invoice = Invoice::create([
            ...$validated,
            'tax_amount' => $validated['tax_amount'] ?? 0,
            'total_amount' => $validated['subtotal'] + ($validated['tax_amount'] ?? 0),
            'status' => 'issued',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('finance.invoices.show', $invoice)
            ->with('success', 'Invoice berhasil dibuat!');
    }

    /**
     * Create invoice from issued PO
     */
    public function createFromPO(PurchaseOrder $po)
    {
        // Check if PO is issued
        if ($po->status !== 'issued') {
            return back()->with('error', 'Hanya PO yang sudah diterbitkan yang bisa dibuat invoice!');
        }

        // Check if invoice already exists for this PO
        if ($po->invoices()->exists()) {
            return back()->with('error', 'Invoice untuk PO ini sudah ada!');
        }

        // Create invoice from PO data
        $invoice = Invoice::create([
            'document_id' => $po->document_id,
            'purchase_order_id' => $po->id,
            'vendor_name' => $po->vendor_name,
            'vendor_address' => $po->vendor_address,
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'subtotal' => $po->subtotal,
            'tax_amount' => $po->tax_amount,
            'total_amount' => $po->total_amount,
            'notes' => 'Invoice dari ' . $po->po_number,
            'status' => 'issued',
            'created_by' => auth()->id(),
        ]);

        // Update PO status
        $po->update(['status' => 'completed']);

        return redirect()->route('finance.invoices.show', $invoice)
            ->with('success', 'Invoice berhasil dibuat dari PO ' . $po->po_number . '!');
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['document', 'purchaseOrder', 'creator']);
        return view('finance.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the invoice.
     */
    public function edit(Invoice $invoice)
    {
        return view('finance.invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified invoice.
     */
    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Invoice yang sudah lunas tidak dapat diubah!');
        }

        // Sanitize currency inputs (strip thousand separators)
        $this->sanitizeMoneyFields($request, ['subtotal', 'tax_amount']);

        $validated = $request->validate([
            'vendor_name' => 'required|string|max:255',
            'vendor_address' => 'nullable|string',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $invoice->update([
            ...$validated,
            'total_amount' => $validated['subtotal'] + ($validated['tax_amount'] ?? 0),
        ]);

        return redirect()->route('finance.invoices.show', $invoice)
            ->with('success', 'Invoice berhasil diperbarui!');
    }

    /**
     * Remove the specified invoice.
     */
    public function destroy(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Invoice yang sudah diterbitkan tidak dapat dihapus!');
        }

        $invoice->delete();

        return redirect()->route('finance.invoices.index')
            ->with('success', 'Invoice berhasil dihapus!');
    }

    /**
     * Record payment for invoice (or mark as fully paid)
     * Fix #9: Wrapped in DB::transaction for data consistency
     * Fix #10: Budget update now incremental per payment
     */
    public function recordPayment(Request $request, Invoice $invoice)
    {
        return DB::transaction(function () use ($request, $invoice) {
            // If no amount provided, mark as fully paid
            if (!$request->filled('amount')) {
                $paymentAmount = $invoice->total_amount - $invoice->paid_amount;

                $invoice->update([
                    'paid_amount' => $invoice->total_amount,
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                // Update document status
                if ($invoice->document) {
                    $invoice->document->update(['status' => 'paid']);
                }

                // Update budget: move from committed to used
                $this->updateBudgetOnPayment($invoice, $paymentAmount);

                return back()->with('success', 'Invoice berhasil ditandai lunas!');
            }

            // Partial payment with amount
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0.01|max:' . $invoice->outstanding_amount,
            ]);

            $paymentAmount = (float) $validated['amount'];

            $invoice->recordPayment($paymentAmount);

            // Update budget incrementally per payment
            $this->updateBudgetOnPayment($invoice, $paymentAmount);

            // Update document status if fully paid
            if ($invoice->isFullyPaid() && $invoice->document) {
                $invoice->document->update(['status' => 'paid']);
            }

            return back()->with('success', 'Pembayaran berhasil dicatat!');
        });
    }

    /**
     * Cancel/Void an invoice (Fix #8)
     *
     * Allows cancellation of issued/partial invoices.
     * Reverts budget committed/used amounts accordingly.
     */
    public function cancel(Request $request, Invoice $invoice)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ], [
            'cancellation_reason.required' => 'Alasan pembatalan wajib diisi.',
        ]);

        // Prevent cancelling already paid or cancelled invoices
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Invoice yang sudah lunas tidak dapat dibatalkan. Gunakan proses refund.');
        }

        if ($invoice->status === 'cancelled') {
            return back()->with('error', 'Invoice ini sudah dibatalkan sebelumnya.');
        }

        return DB::transaction(function () use ($request, $invoice) {
            $previousPaidAmount = $invoice->paid_amount;

            // Update invoice status
            $invoice->update([
                'status' => 'cancelled',
                'notes' => ($invoice->notes ? $invoice->notes . "\n\n" : '') .
                    '[DIBATALKAN ' . now()->format('d/m/Y H:i') . '] ' . $request->cancellation_reason,
            ]);

            // Revert budget changes if there was budget impact
            if ($invoice->document) {
                $budget = Budget::active()
                    ->forYear(date('Y'))
                    ->forDepartment($invoice->document->department)
                    ->first();

                if ($budget) {
                    // Revert used_amount (jika ada partial payment sebelumnya)
                    if ($previousPaidAmount > 0) {
                        $budget->decrement('used_amount', min($previousPaidAmount, $budget->used_amount));
                    }

                    // Revert committed_budget (kembalikan outstanding ke committed)
                    $outstandingBeforeCancel = $invoice->total_amount - $previousPaidAmount;
                    if ($outstandingBeforeCancel > 0) {
                        $budget->decrement('committed_budget', min($outstandingBeforeCancel, $budget->committed_budget));
                    }
                }

                // Reset document status back to approved
                $invoice->document->update(['status' => 'approved']);
            }

            return back()->with('success', 'Invoice berhasil dibatalkan.');
        });
    }

    /**
     * Update budget when payment is recorded
     * Fix #10: Now called incrementally per payment, not just on full payment
     */
    protected function updateBudgetOnPayment(Invoice $invoice, float $amount): void
    {
        if (!$invoice->document || $amount <= 0) {
            return;
        }

        $budget = Budget::active()
            ->forYear(date('Y'))
            ->forDepartment($invoice->document->department)
            ->first();

        if ($budget) {
            // Increment used_budget (money spent)
            $budget->increment('used_amount', $amount);

            // Decrement committed_budget (was reserved, now spent)
            $currentCommitted = $budget->committed_budget ?? 0;
            $decrementAmount = min($amount, $currentCommitted);
            if ($decrementAmount > 0) {
                $budget->decrement('committed_budget', $decrementAmount);
            }
        }
    }

    /**
     * Print invoice
     */
    public function print(Invoice $invoice)
    {
        $invoice->load(['document', 'purchaseOrder']);

        $verifyUrl = route('verify.show', $invoice->approval_hash ?? $invoice->document->approval_hash ?? 'invalid');
        $qrCode = base64_encode(QrCode::format('svg')->size(80)->generate($verifyUrl));

        // Force English locale for PDF to avoid CJK font issues in DomPDF
        app()->setLocale('en');

        return $this->renderPdfWithPageCounter('pdf.invoice', [
            'invoice' => $invoice,
            'qrCode' => $qrCode,
        ], $invoice->invoice_number . '.pdf');
    }
}
