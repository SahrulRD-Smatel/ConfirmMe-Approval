<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Document;
use App\Models\Budget;
use App\Traits\PdfPageCounter;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PurchaseOrderController extends Controller
{
    use PdfPageCounter;
    /**
     * Display a listing of purchase orders.
     */
    public function index(Request $request)
    {
        $purchaseOrders = PurchaseOrder::with(['document', 'creator'])
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('po_number', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Get approved documents that need PO generation
        $pendingDocuments = Document::where('status', 'approved')
            ->where('approval_type', 'po')
            ->whereDoesntHave('purchaseOrder')
            ->with('user')
            ->latest()
            ->get();

        return view('finance.po.index', compact('purchaseOrders', 'pendingDocuments'));
    }

    /**
     * Show the form for creating a new purchase order.
     */
    public function create(Request $request)
    {
        $document = null;
        if ($request->filled('document_id')) {
            $document = Document::findOrFail($request->document_id);
        }

        $documents = Document::where('approval_type', 'po')
            ->where('status', 'approved')
            ->whereDoesntHave('purchaseOrder')
            ->get();

        return view('finance.po.create', compact('document', 'documents'));
    }

    /**
     * Store a newly created purchase order.
     */
    public function store(Request $request)
    {
        // Sanitize currency inputs (strip thousand separators)
        $this->sanitizeMoneyFields($request, ['tax_amount']);

        $validated = $request->validate([
            'document_id' => 'required|exists:documents,id',
            'vendor_name' => 'required|string|max:255',
            'vendor_address' => 'nullable|string',
            'vendor_phone' => 'nullable|string|max:20',
            'vendor_email' => 'nullable|email',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'tax_amount' => 'nullable|numeric|min:0',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'required|string|max:20',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Calculate total before creating PO
        $subtotal = collect($validated['items'])->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'];
        });
        $taxAmount = $validated['tax_amount'] ?? 0;
        $totalAmount = $subtotal + $taxAmount;

        // Validate against document's approved budget
        $document = Document::find($validated['document_id']);
        if ($document && $document->approved_budget > 0) {
            if ($totalAmount > $document->approved_budget) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'total' => 'Total PO (Rp ' . number_format($totalAmount, 0, ',', '.') .
                            ') melebihi budget yang disetujui (Rp ' .
                            number_format($document->approved_budget, 0, ',', '.') . ')!'
                    ]);
            }
        }

        $po = PurchaseOrder::create([
            'document_id' => $validated['document_id'],
            'vendor_name' => $validated['vendor_name'],
            'vendor_address' => $validated['vendor_address'],
            'vendor_phone' => $validated['vendor_phone'],
            'vendor_email' => $validated['vendor_email'],
            'order_date' => $validated['order_date'],
            'expected_delivery_date' => $validated['expected_delivery_date'],
            'tax_amount' => $taxAmount,
            'terms_conditions' => $validated['terms_conditions'],
            'notes' => $validated['notes'],
            'subtotal' => $subtotal,
            'total_amount' => $totalAmount,
            'created_by' => auth()->id(),
        ]);

        foreach ($validated['items'] as $item) {
            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'item_name' => $item['item_name'],
                'description' => $item['description'] ?? null,
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return redirect()->route('finance.po.show', $po)
            ->with('success', 'Purchase Order berhasil dibuat!');
    }

    /**
     * Display the specified purchase order.
     */
    public function show(PurchaseOrder $po)
    {
        $po->load(['document', 'creator', 'items']);
        return view('finance.po.show', compact('po'));
    }

    /**
     * Show the form for editing the purchase order.
     */
    public function edit(PurchaseOrder $po)
    {
        $po->load('items');
        return view('finance.po.edit', compact('po'));
    }

    /**
     * Update the specified purchase order.
     */
    public function update(Request $request, PurchaseOrder $po)
    {
        if ($po->status !== 'draft') {
            return back()->with('error', 'PO yang sudah diterbitkan tidak dapat diubah!');
        }

        // Sanitize currency inputs (strip thousand separators)
        $this->sanitizeMoneyFields($request, ['tax_amount']);

        $validated = $request->validate([
            'vendor_name' => 'required|string|max:255',
            'vendor_address' => 'nullable|string',
            'vendor_phone' => 'nullable|string|max:20',
            'vendor_email' => 'nullable|email',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'tax_amount' => 'nullable|numeric|min:0',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit' => 'required|string|max:50',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Calculate total before updating
        $subtotal = collect($validated['items'])->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'];
        });
        $taxAmount = $validated['tax_amount'] ?? 0;
        $totalAmount = $subtotal + $taxAmount;

        // Validate against document's approved budget
        if ($po->document && $po->document->approved_budget > 0) {
            if ($totalAmount > $po->document->approved_budget) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'total' => 'Total PO (Rp ' . number_format($totalAmount, 0, ',', '.') .
                            ') melebihi budget yang disetujui (Rp ' .
                            number_format($po->document->approved_budget, 0, ',', '.') . ')!'
                    ]);
            }
        }

        // Update PO data
        $po->update([
            'vendor_name' => $validated['vendor_name'],
            'vendor_address' => $validated['vendor_address'] ?? null,
            'vendor_phone' => $validated['vendor_phone'] ?? null,
            'vendor_email' => $validated['vendor_email'] ?? null,
            'order_date' => $validated['order_date'],
            'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
            'tax_amount' => $taxAmount,
            'terms_conditions' => $validated['terms_conditions'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'subtotal' => $subtotal,
            'total_amount' => $totalAmount,
        ]);

        // Update items - delete old and create new
        $po->items()->delete();

        foreach ($validated['items'] as $itemData) {
            $po->items()->create([
                'item_name' => $itemData['item_name'],
                'quantity' => $itemData['quantity'],
                'unit' => $itemData['unit'],
                'unit_price' => $itemData['unit_price'],
                'total_price' => $itemData['quantity'] * $itemData['unit_price'],
            ]);
        }

        return redirect()->route('finance.po.show', $po)
            ->with('success', 'Purchase Order berhasil diperbarui!');
    }

    /**
     * Remove the specified purchase order.
     */
    public function destroy(PurchaseOrder $po)
    {
        if ($po->status !== 'draft') {
            return back()->with('error', 'PO yang sudah diterbitkan tidak dapat dihapus!');
        }

        $po->delete();

        return redirect()->route('finance.po.index')
            ->with('success', 'Purchase Order berhasil dihapus!');
    }

    /**
     * Issue the purchase order
     */
    public function issue(PurchaseOrder $po)
    {
        if ($po->status !== 'draft') {
            return back()->with('error', 'PO sudah diterbitkan sebelumnya!');
        }

        $po->update([
            'status' => 'issued',
            'approval_hash' => hash('sha256', $po->po_number . now()->toISOString()),
        ]);

        // Auto-commit budget saat PO diterbitkan
        $this->commitBudget($po);

        return back()->with('success', 'Purchase Order berhasil diterbitkan!');
    }

    /**
     * Commit budget when PO is issued
     */
    protected function commitBudget(PurchaseOrder $po): void
    {
        if (!$po->document) {
            return;
        }

        $budget = Budget::active()
            ->forYear(date('Y'))
            ->forDepartment($po->document->department)
            ->first();

        if ($budget) {
            $budget->increment('committed_budget', $po->total_amount);
        }
    }

    /**
     * Print purchase order
     */
    public function print(PurchaseOrder $po)
    {
        $po->load(['document', 'items']);

        $verifyUrl = route('verify.show', $po->approval_hash ?? $po->document->approval_hash ?? 'invalid');
        $qrCode = base64_encode(QrCode::format('svg')->size(80)->generate($verifyUrl));

        // Force English locale for PDF to avoid CJK font issues in DomPDF
        app()->setLocale('en');

        return $this->renderPdfWithPageCounter('pdf.purchase-order', [
            'po' => $po,
            'qrCode' => $qrCode,
        ], $po->po_number . '.pdf');
    }
}
