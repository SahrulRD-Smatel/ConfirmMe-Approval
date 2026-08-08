<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentAttachment;
use App\Models\DocumentTemplate;
use App\Models\ApprovalLog;
use App\Notifications\DocumentNotification;
use App\Services\ApprovalWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DocumentController extends Controller
{
    protected $workflowService;

    public function __construct(ApprovalWorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * Display a listing of the documents.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // "Default Pengajuan" should only show MY documents
        $query = Document::with(['user', 'currentApprover'])
            ->where('user_id', $user->id)
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('approval_type'), function ($q) use ($request) {
                $q->where('approval_type', $request->approval_type);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('title', 'like', '%' . $request->search . '%')
                        ->orWhere('document_number', 'like', '%' . $request->search . '%');
                });
            })
            ->latest();

        $documents = $query->paginate(15)->withQueryString();

        return view('documents.index', compact('documents'));
    }

    /**
     * Show the form for creating a new document.
     */
    public function create()
    {
        $this->authorize('create', Document::class);

        $templates = DocumentTemplate::active()->get();

        // Determine which approval types this user can create based on department
        $typeAccess = \App\Models\WorkflowSetting::getTypeAccess();
        $userDept = auth()->user()->department;
        $allowedTypes = [];

        foreach ($typeAccess as $type => $departments) {
            if (in_array('*', $departments) || in_array($userDept, $departments)) {
                $allowedTypes[] = $type;
            }
        }

        return view('documents.create', compact('templates', 'allowedTypes'));
    }

    /**
     * Get template data as JSON for auto-fill.
     */
    public function getTemplate(string $code)
    {
        $template = DocumentTemplate::where('code', $code)->where('is_active', true)->first();

        if (!$template) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        return response()->json([
            'name' => $template->name,
            'approval_type' => $template->approval_type,
            'description' => $template->description,
            'fields' => $template->fields ?? [],
        ]);
    }

    /**
     * Store a newly created document in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Document::class);

        // Validate approval type access based on user's department
        $typeAccess = config('workflow.type_access', []);
        $userDept = auth()->user()->department;
        $requestedType = $request->input('approval_type');
        $allowedDepts = $typeAccess[$requestedType] ?? ['*'];

        if (!in_array('*', $allowedDepts) && !in_array($userDept, $allowedDepts)) {
            abort(403, 'Departemen Anda tidak memiliki akses untuk membuat pengajuan tipe ini.');
        }

        // Core validation rules
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'approval_type' => 'required|in:only,po,invoice,loan',
            'po_category' => 'nullable|in:internal,external',
            'priority' => 'required|in:low,normal,high,urgent',
            'template' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            // Type-specific fields (all nullable, will validate based on type)
            'document_category' => 'nullable|string',
            'effective_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'related_department' => 'nullable|string',
            'vendor_name' => 'nullable|string|max:255',
            'vendor_npwp' => 'nullable|string|max:50',
            'item_description' => 'nullable|string',
            'subtotal' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'budget_code' => 'nullable|string|max:50',
            'expected_delivery' => 'nullable|date',
            'invoice_number' => 'nullable|string|max:100',
            'invoice_date' => 'nullable|date',
            'related_po_number' => 'nullable|string|max:100',
            'vendor_name_invoice' => 'nullable|string|max:255',
            'invoice_amount' => 'nullable|numeric|min:0',
            'payment_due_date' => 'nullable|date',
            'bank_account' => 'nullable|string|max:255',
            'tax_invoice' => 'nullable|string|max:100',
            'loan_type' => 'nullable|string',
            'loan_amount' => 'nullable|numeric|min:0',
            'installment_count' => 'nullable|integer|min:1',
            'repayment_start' => 'nullable|string',
            'loan_reason' => 'nullable|string',
        ];

        $validated = $request->validate($rules);

        // Sanitize currency inputs (strip thousand separators like dots)
        $this->sanitizeMoneyFields($request, ['subtotal', 'tax_amount', 'invoice_amount', 'loan_amount']);

        // Build metadata based on approval type
        $metadata = [];

        switch ($validated['approval_type']) {
            case 'only':
                $metadata = [
                    'document_category' => $request->input('document_category'),
                    'effective_date' => $request->input('effective_date'),
                    'expiry_date' => $request->input('expiry_date'),
                    'related_department' => $request->input('related_department'),
                ];
                break;
            case 'po':
                $metadata = [
                    'vendor_name' => $request->input('vendor_name'),
                    'vendor_npwp' => $request->input('vendor_npwp'),
                    'item_description' => $request->input('item_description'),
                    'subtotal' => $request->input('subtotal'),
                    'tax_amount' => $request->input('tax_amount'),
                    'budget_code' => $request->input('budget_code'),
                    'expected_delivery' => $request->input('expected_delivery'),
                ];
                break;
            case 'invoice':
                $metadata = [
                    'invoice_number' => $request->input('invoice_number'),
                    'invoice_date' => $request->input('invoice_date'),
                    'related_po_number' => $request->input('related_po_number'),
                    'vendor_name' => $request->input('vendor_name_invoice'),
                    'invoice_amount' => $request->input('invoice_amount'),
                    'payment_due_date' => $request->input('payment_due_date'),
                    'bank_account' => $request->input('bank_account'),
                    'tax_invoice' => $request->input('tax_invoice'),
                ];
                break;
            case 'loan':
                $metadata = [
                    'loan_type' => $request->input('loan_type'),
                    'loan_amount' => $request->input('loan_amount'),
                    'installment_count' => $request->input('installment_count'),
                    'repayment_start' => $request->input('repayment_start'),
                    'loan_reason' => $request->input('loan_reason'),
                ];
                break;
        }

        // Auto-calculate estimated_budget from type-specific nominals
        $estimatedBudget = match ($validated['approval_type']) {
            'po' => ($request->input('subtotal', 0) + $request->input('tax_amount', 0)),
            'invoice' => $request->input('invoice_amount', 0) ?: 0,
            'loan' => $request->input('loan_amount', 0) ?: 0,
            default => null,
        };

        $document = Document::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'approval_type' => $validated['approval_type'],
            'po_category' => $validated['po_category'] ?? 'external',
            'priority' => $validated['priority'],
            'estimated_budget' => $estimatedBudget,
            'template' => $validated['template'] ?? null,
            'user_id' => auth()->id(),
            'department' => auth()->user()->department,
            'status' => 'draft',
            'metadata' => $metadata,
        ]);

        // Handle file uploads safely
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');
            \Log::info('File upload started. Files received: ' . (is_array($files) ? count($files) : 'single'));

            // Ensure it's an array
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $index => $file) {
                try {
                    // Multiple validation checks with logging
                    if (!$file) {
                        \Log::warning("File[$index]: Skipped - file is null");
                        continue;
                    }
                    if (!($file instanceof \Illuminate\Http\UploadedFile)) {
                        \Log::warning("File[$index]: Skipped - not UploadedFile instance, type: " . gettype($file));
                        continue;
                    }
                    if (!$file->isValid()) {
                        \Log::warning("File[$index]: Skipped - isValid() returned false, error: " . $file->getError());
                        continue;
                    }
                    if ($file->getError() !== UPLOAD_ERR_OK) {
                        \Log::warning("File[$index]: Skipped - getError() = " . $file->getError());
                        continue;
                    }
                    if (empty($file->getClientOriginalName())) {
                        \Log::warning("File[$index]: Skipped - getClientOriginalName() is empty");
                        continue;
                    }

                    // Get the temp path - try multiple methods for Windows compatibility
                    $tempPath = $file->getRealPath() ?: $file->getPathname() ?: $file->path();
                    if (empty($tempPath) || !file_exists($tempPath)) {
                        \Log::warning("File[$index]: Skipped - no valid temp path found");
                        continue;
                    }

                    $fileSize = $file->getSize() ?: filesize($tempPath);
                    if ($fileSize <= 0) {
                        \Log::warning("File[$index]: Skipped - file size is 0");
                        continue;
                    }

                    // Generate unique filename
                    $extension = $file->getClientOriginalExtension();
                    $filename = uniqid() . '_' . time() . '.' . $extension;
                    $storagePath = 'documents/' . $document->id . '/' . $filename;

                    \Log::info("File[$index]: Storing {$file->getClientOriginalName()} to {$storagePath}");

                    // Use file_get_contents + Storage::put for Windows compatibility
                    $contents = file_get_contents($tempPath);
                    if ($contents === false) {
                        \Log::warning("File[$index]: Skipped - could not read file contents");
                        continue;
                    }

                    $stored = Storage::disk('public')->put($storagePath, $contents);
                    if (!$stored) {
                        \Log::warning("File[$index]: Skipped - Storage::put() failed");
                        continue;
                    }

                    DocumentAttachment::create([
                        'document_id' => $document->id,
                        'filename' => $filename,
                        'original_filename' => $file->getClientOriginalName(),
                        'file_path' => $storagePath,
                        'file_type' => $extension,
                        'file_size' => $fileSize,
                        'uploaded_by' => auth()->id(),
                    ]);
                    \Log::info("File[$index]: Successfully uploaded to $storagePath");
                } catch (\Throwable $e) {
                    \Log::error("File[$index]: Exception - " . $e->getMessage());
                    continue;
                }
            }
        } else {
            \Log::info('No attachments in request');
        }

        ApprovalLog::log($document, 'created', 'Dokumen dibuat');

        return redirect()->route('documents.show', $document)
            ->with('success', 'Dokumen berhasil dibuat!');
    }

    /**
     * Display the specified document.
     */
    public function show(Document $document)
    {
        $this->authorize('view', $document);

        $document->load([
            'user',
            'currentApprover',
            'approvals.approver',
            'attachments.uploader',
            'comments.user',
            'approvalLogs.user',
        ]);

        return view('documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified document.
     */
    public function edit(Document $document)
    {
        $this->authorize('update', $document);

        $templates = DocumentTemplate::active()->get();

        return view('documents.edit', compact('document', 'templates'));
    }

    /**
     * Update the specified document in storage.
     */
    public function update(Request $request, Document $document)
    {
        $this->authorize('update', $document);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'approval_type' => 'required|in:only,po,invoice,loan',
            'priority' => 'required|in:low,normal,high,urgent',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        // Sanitize currency inputs (strip thousand separators like dots)
        $this->sanitizeMoneyFields($request, ['subtotal', 'tax_amount', 'invoice_amount', 'loan_amount']);

        // Auto-calculate estimated_budget from type-specific nominals
        $estimatedBudget = match ($validated['approval_type']) {
            'po' => ($request->input('subtotal', 0) + $request->input('tax_amount', 0)),
            'invoice' => $request->input('invoice_amount', 0) ?: 0,
            'loan' => $request->input('loan_amount', 0) ?: 0,
            default => null,
        };

        // Update document (exclude attachments as they're handled separately)
        $document->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'approval_type' => $validated['approval_type'],
            'priority' => $validated['priority'],
            'estimated_budget' => $estimatedBudget,
        ]);

        // Handle new file uploads safely
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');
            \Log::info('Update: File upload started. Files received: ' . (is_array($files) ? count($files) : 'single'));

            // Ensure it's an array
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $index => $file) {
                try {
                    // Multiple validation checks
                    if (!$file) {
                        \Log::warning("Update File[$index]: Skipped - file is null");
                        continue;
                    }
                    if (!($file instanceof \Illuminate\Http\UploadedFile)) {
                        \Log::warning("Update File[$index]: Skipped - not UploadedFile instance");
                        continue;
                    }
                    if (!$file->isValid()) {
                        \Log::warning("Update File[$index]: Skipped - isValid() returned false");
                        continue;
                    }
                    if ($file->getError() !== UPLOAD_ERR_OK) {
                        \Log::warning("Update File[$index]: Skipped - getError() = " . $file->getError());
                        continue;
                    }
                    if (empty($file->getClientOriginalName())) {
                        \Log::warning("Update File[$index]: Skipped - getClientOriginalName() is empty");
                        continue;
                    }

                    // Get the temp path - try multiple methods for Windows compatibility
                    $tempPath = $file->getRealPath() ?: $file->getPathname() ?: $file->path();
                    if (empty($tempPath) || !file_exists($tempPath)) {
                        \Log::warning("Update File[$index]: Skipped - no valid temp path found");
                        continue;
                    }

                    $fileSize = $file->getSize() ?: filesize($tempPath);
                    if ($fileSize <= 0) {
                        \Log::warning("Update File[$index]: Skipped - file size is 0");
                        continue;
                    }

                    // Generate unique filename
                    $extension = $file->getClientOriginalExtension();
                    $filename = uniqid() . '_' . time() . '.' . $extension;
                    $storagePath = 'documents/' . $document->id . '/' . $filename;

                    \Log::info("Update File[$index]: Storing {$file->getClientOriginalName()} to {$storagePath}");

                    // Use file_get_contents + Storage::put for Windows compatibility
                    $contents = file_get_contents($tempPath);
                    if ($contents === false) {
                        \Log::warning("Update File[$index]: Skipped - could not read file contents");
                        continue;
                    }

                    $stored = Storage::disk('public')->put($storagePath, $contents);
                    if (!$stored) {
                        \Log::warning("Update File[$index]: Skipped - Storage::put() failed");
                        continue;
                    }

                    DocumentAttachment::create([
                        'document_id' => $document->id,
                        'filename' => $filename,
                        'original_filename' => $file->getClientOriginalName(),
                        'file_path' => $storagePath,
                        'file_type' => $extension,
                        'file_size' => $fileSize,
                        'uploaded_by' => auth()->id(),
                    ]);
                    \Log::info("Update File[$index]: Successfully uploaded to $storagePath");
                } catch (\Throwable $e) {
                    \Log::error('Update file upload error: ' . $e->getMessage());
                    continue;
                }
            }
        }

        ApprovalLog::log($document, 'updated', 'Dokumen diperbarui');

        return redirect()->route('documents.show', $document)
            ->with('success', 'Dokumen berhasil diperbarui!');
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        // Soft delete
        $document->delete();

        ApprovalLog::log($document, 'deleted', 'Dokumen dihapus');

        // Hapus notifikasi terkait
        \Illuminate\Support\Facades\DB::table('notifications')
            ->where('data->document_id', $document->id)
            ->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Dokumen berhasil dihapus!');
    }

    /**
     * Submit document for approval
     */
    public function submit(Document $document)
    {
        $this->authorize('submit', $document);

        // Check if this is a re-submit after revision
        if ($document->isRevision()) {
            // Re-submit goes back to the approver who requested revision
            $this->workflowService->resubmitAfterRevision($document);

            ApprovalLog::log($document, 'resubmitted', 'Dokumen dikirim ulang setelah revisi (revisi ke-' . $document->revision_count . ')');
        } else {
            // Initialize new workflow
            $this->workflowService->initializeWorkflow($document);

            $document->update([
                'status' => 'pending',
                'submitted_at' => now(),
            ]);

            ApprovalLog::log($document, 'submitted', 'Dokumen dikirim untuk approval');
        }

        // Note: Email notification to first approver is sent in initializeWorkflow()

        // Send in-app notification to first/current approver
        try {
            $document->refresh();
            if ($document->currentApprover) {
                $document->currentApprover->notify(new DocumentNotification(
                    'submitted',
                    $document,
                    auth()->user()->name . " mengajukan dokumen \"{$document->title}\" untuk persetujuan Anda.",
                    route('documents.show', $document)
                ));
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to send submit notification: ' . $e->getMessage());
        }

        $message = $document->revision_count > 0
            ? 'Dokumen berhasil dikirim ulang setelah revisi!'
            : 'Dokumen berhasil dikirim untuk approval!';

        return redirect()->route('documents.show', $document)
            ->with('success', $message);
    }

    /**
     * Print document as PDF
     */
    public function print(Document $document)
    {
        $this->authorize('print', $document);

        // Generate QR code using SVG format (doesn't require Imagick)
        $verifyUrl = route('verify.show', $document->approval_hash ?? $document->generateApprovalHash());
        $qrCode = base64_encode(QrCode::format('svg')->size(100)->generate($verifyUrl));

        $pdf = Pdf::loadView('pdf.document', [
            'document' => $document->load(['user', 'approvals.approver']),
            'qrCode' => $qrCode,
            'qrFormat' => 'svg',
        ]);

        return $pdf->stream($document->document_number . '.pdf');
    }

    /**
     * Download document PDF
     */
    public function download(Document $document)
    {
        $this->authorize('print', $document);

        $verifyUrl = route('verify.show', $document->approval_hash ?? $document->generateApprovalHash());
        $qrCode = base64_encode(QrCode::format('svg')->size(100)->generate($verifyUrl));

        $pdf = Pdf::loadView('pdf.document', [
            'document' => $document->load(['user', 'approvals.approver']),
            'qrCode' => $qrCode,
            'qrFormat' => 'svg',
        ]);

        return $pdf->download($document->document_number . '.pdf');
    }

    /**
     * Add comment to document
     */
    public function comment(Request $request, Document $document)
    {
        $this->authorize('view', $document);

        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
            'is_internal' => 'boolean',
        ]);

        \App\Models\DocumentComment::create([
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'comment' => $validated['comment'],
            'is_internal' => $validated['is_internal'] ?? false,
        ]);

        ApprovalLog::log($document, 'commented', 'Komentar ditambahkan');

        return back()->with('success', 'Komentar berhasil ditambahkan!');
    }

    /**
     * Download attachment file
     */
    public function downloadAttachment(DocumentAttachment $attachment)
    {
        // Check if user can view the document
        $this->authorize('view', $attachment->document);

        // Get full file path
        $filePath = Storage::disk('public')->path($attachment->file_path);

        // Check if file exists
        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download(
            $filePath,
            $attachment->original_filename
        );
    }

    /**
     * Requester accepts the revised nominal amount.
     * Document continues to the next approval step.
     */
    public function confirmAmount(Document $document)
    {
        // Only the document owner can confirm
        if ((int) $document->user_id !== (int) auth()->id()) {
            abort(403, 'Hanya pembuat dokumen yang dapat mengkonfirmasi nominal.');
        }

        if ($document->status !== 'pending_requester_confirmation') {
            return back()->with('error', 'Dokumen ini tidak dalam status menunggu konfirmasi nominal.');
        }

        $workflowService = app(ApprovalWorkflowService::class);
        $workflowService->processRequesterConfirmation($document, 'accept');

        $amount = number_format($document->confirmed_amount, 0, ',', '.');

        return back()->with('success', "Nominal Rp {$amount} disetujui. Dokumen dilanjutkan ke tahap berikutnya.");
    }

    /**
     * Requester rejects the revised nominal amount.
     * Document is cancelled/rejected.
     */
    public function rejectAmount(Document $document)
    {
        // Only the document owner can reject
        if ((int) $document->user_id !== (int) auth()->id()) {
            abort(403, 'Hanya pembuat dokumen yang dapat menolak nominal.');
        }

        if ($document->status !== 'pending_requester_confirmation') {
            return back()->with('error', 'Dokumen ini tidak dalam status menunggu konfirmasi nominal.');
        }

        $workflowService = app(ApprovalWorkflowService::class);
        $workflowService->processRequesterConfirmation($document, 'reject');

        return back()->with('success', 'Nominal ditolak. Dokumen dibatalkan.');
    }
}

