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
use Illuminate\Support\Facades\DB;

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

        // Determine which approval types this user can create based on department
        $typeAccess = \App\Models\WorkflowSetting::getTypeAccess();
        $userDept = auth()->user()->department;
        $allowedTypes = [];

        foreach ($typeAccess as $type => $departments) {
            if (in_array('*', $departments) || in_array($userDept, $departments)) {
                $allowedTypes[] = $type;
            }
        }

        // Apply global feature toggles
        if (!feature_active('module_loan')) {
            $allowedTypes = array_diff($allowedTypes, ['loan']);
        }
        if (!feature_active('module_finance')) {
            $allowedTypes = array_diff($allowedTypes, ['po', 'invoice']);
        }

        // Fetch active templates that belong to the allowed approval types
        $templates = DocumentTemplate::active()
            ->whereIn('approval_type', $allowedTypes)
            ->get();

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
     * Store newly created documents in storage (Bulk Request).
     */
    public function store(Request $request)
    {
        $this->authorize('create', Document::class);

        // We wrap everything in a transaction for bulk requests
        return DB::transaction(function () use ($request) {
            // First, validate the array of documents
            $rules = [
                'documents' => 'required|array|min:1|max:10',
                'documents.*.title' => 'required|string|max:255',
                'documents.*.description' => 'required|string',
                'documents.*.approval_type' => 'required|in:only,po,invoice,loan',
                'documents.*.po_category' => 'nullable|in:internal,external',
                'documents.*.priority' => 'required|in:low,normal,high,urgent',
                'documents.*.template' => 'nullable|string',
                // Type-specific fields
                'documents.*.document_category' => 'nullable|string',
                'documents.*.effective_date' => 'nullable|date',
                'documents.*.expiry_date' => 'nullable|date',
                'documents.*.related_department' => 'nullable|string',
                'documents.*.vendor_name' => 'nullable|string|max:255',
                'documents.*.vendor_npwp' => 'nullable|string|max:50',
                'documents.*.item_description' => 'nullable|string',
                'documents.*.subtotal' => 'nullable|numeric|min:0',
                'documents.*.tax_amount' => 'nullable|numeric|min:0',
                'documents.*.budget_code' => 'nullable|string|max:50',
                'documents.*.expected_delivery' => 'nullable|date',
                'documents.*.invoice_number' => 'nullable|string|max:100',
                'documents.*.invoice_date' => 'nullable|date',
                'documents.*.related_po_number' => 'nullable|string|max:100',
                'documents.*.vendor_name_invoice' => 'nullable|string|max:255',
                'documents.*.invoice_amount' => 'nullable|numeric|min:0',
                'documents.*.payment_due_date' => 'nullable|date',
                'documents.*.bank_account' => 'nullable|string|max:255',
                'documents.*.tax_invoice' => 'nullable|string|max:100',
                'documents.*.loan_type' => 'nullable|string',
                'documents.*.loan_amount' => 'nullable|numeric|min:0',
                'documents.*.installment_count' => 'nullable|integer|min:1',
                'documents.*.repayment_start' => 'nullable|string',
                'documents.*.loan_reason' => 'nullable|string',
                'documents.*.metadata' => 'nullable|array',
                'documents.*.metadata_files' => 'nullable|array',
                'documents.*.metadata_files.*' => 'nullable|file|max:10240',
                
                // Attachments are indexed by document index
                'attachments' => 'nullable|array|max:10',
                'attachments.*' => 'nullable|array',
                'attachments.*.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            ];

            $validated = $request->validate($rules, [
                'documents.*.title.required' => 'Judul dokumen wajib diisi.',
                'documents.*.description.required' => 'Deskripsi dokumen wajib diisi.',
            ]);

            $typeAccess = config('workflow.type_access', []);
            $userDept = auth()->user()->department;
            
            // Validate department access for ALL requested documents
            foreach ($validated['documents'] as $docData) {
                $requestedType = $docData['approval_type'];
                $allowedDepts = $typeAccess[$requestedType] ?? ['*'];

                if (!in_array('*', $allowedDepts) && !in_array($userDept, $allowedDepts)) {
                    abort(403, 'Departemen Anda tidak memiliki akses untuk membuat pengajuan tipe ini.');
                }
            }

            foreach ($validated['documents'] as $index => $docData) {
                // Sanitize currency inputs for this specific document
                $moneyFields = ['subtotal', 'tax_amount', 'invoice_amount', 'loan_amount'];
                foreach ($moneyFields as $field) {
                    if (isset($docData[$field])) {
                        $docData[$field] = str_replace('.', '', $docData[$field]);
                    }
                }

                // Build metadata based on approval type
                $metadata = $docData['metadata'] ?? []; // Start with dynamic metadata from builder
                switch ($docData['approval_type']) {
                    case 'only':
                        $metadata = array_merge($metadata, [
                            'document_category' => $docData['document_category'] ?? null,
                            'effective_date' => $docData['effective_date'] ?? null,
                            'expiry_date' => $docData['expiry_date'] ?? null,
                            'related_department' => $docData['related_department'] ?? null,
                        ]);
                        break;
                    case 'po':
                        $metadata = array_merge($metadata, [
                            'vendor_name' => $docData['vendor_name'] ?? null,
                            'vendor_npwp' => $docData['vendor_npwp'] ?? null,
                            'item_description' => $docData['item_description'] ?? null,
                            'subtotal' => $docData['subtotal'] ?? 0,
                            'tax_amount' => $docData['tax_amount'] ?? 0,
                            'budget_code' => $docData['budget_code'] ?? null,
                            'expected_delivery' => $docData['expected_delivery'] ?? null,
                        ]);
                        break;
                    case 'invoice':
                        $metadata = array_merge($metadata, [
                            'invoice_number' => $docData['invoice_number'] ?? null,
                            'invoice_date' => $docData['invoice_date'] ?? null,
                            'related_po_number' => $docData['related_po_number'] ?? null,
                            'vendor_name' => $docData['vendor_name_invoice'] ?? null,
                            'invoice_amount' => $docData['invoice_amount'] ?? 0,
                            'payment_due_date' => $docData['payment_due_date'] ?? null,
                            'bank_account' => $docData['bank_account'] ?? null,
                            'tax_invoice' => $docData['tax_invoice'] ?? null,
                        ]);
                        break;
                    case 'loan':
                        $metadata = array_merge($metadata, [
                            'loan_type' => $docData['loan_type'] ?? null,
                            'loan_amount' => $docData['loan_amount'] ?? 0,
                            'installment_count' => $docData['installment_count'] ?? 1,
                            'repayment_start' => $docData['repayment_start'] ?? null,
                            'loan_reason' => $docData['loan_reason'] ?? null,
                        ]);
                        break;
                }

                // Auto-calculate estimated_budget
                $estimatedBudget = match ($docData['approval_type']) {
                    'po' => (($docData['subtotal'] ?? 0) + ($docData['tax_amount'] ?? 0)),
                    'invoice' => $docData['invoice_amount'] ?? 0,
                    'loan' => $docData['loan_amount'] ?? 0,
                    default => null,
                };

                $document = Document::create([
                    'title' => $docData['title'],
                    'description' => $docData['description'],
                    'approval_type' => $docData['approval_type'],
                    'po_category' => $docData['po_category'] ?? 'external',
                    'priority' => $docData['priority'],
                    'estimated_budget' => $estimatedBudget ?: 0,
                    'template' => $docData['template'] ?? null,
                    'user_id' => auth()->id(),
                    'department' => auth()->user()->department,
                    'status' => 'draft',
                    'metadata' => $metadata,
                ]);

                // Handle file uploads for this specific document index
                if ($request->hasFile("attachments.{$index}")) {
                    $files = $request->file("attachments.{$index}");
                    if (!is_array($files)) {
                        $files = [$files];
                    }

                    foreach ($files as $fileIndex => $file) {
                        try {
                            if (!$file || !($file instanceof \Illuminate\Http\UploadedFile) || !$file->isValid()) continue;

                            $tempPath = $file->getRealPath() ?: $file->getPathname() ?: $file->path();
                            if (empty($tempPath) || !file_exists($tempPath)) continue;

                            $extension = $file->getClientOriginalExtension();
                            $filename = uniqid() . '_' . time() . '.' . $extension;
                            $storagePath = 'documents/' . $document->id . '/' . $filename;

                            $contents = file_get_contents($tempPath);
                            if ($contents === false) continue;

                            if (Storage::disk('public')->put($storagePath, $contents)) {
                                DocumentAttachment::create([
                                    'document_id' => $document->id,
                                    'file_name' => $file->getClientOriginalName(),
                                    'file_path' => $storagePath,
                                    'file_type' => $extension,
                                    'file_size' => $file->getSize() ?: filesize($tempPath),
                                    'uploaded_by' => auth()->id(),
                                ]);
                            }
                        } catch (\Exception $e) {
                            \Log::error("Failed to upload file for doc index {$index}: " . $e->getMessage());
                        }
                    }
                }
                
                // Handle dynamic file uploads
                if ($request->hasFile("documents.{$index}.metadata_files")) {
                    $dynamicFiles = $request->file("documents.{$index}.metadata_files");
                    foreach ($dynamicFiles as $fieldName => $file) {
                        try {
                            if (!$file || !($file instanceof \Illuminate\Http\UploadedFile) || !$file->isValid()) continue;
                            $tempPath = $file->getRealPath() ?: $file->getPathname() ?: $file->path();
                            if (empty($tempPath) || !file_exists($tempPath)) continue;

                            $extension = $file->getClientOriginalExtension();
                            $filename = uniqid() . '_df_' . time() . '.' . $extension;
                            $storagePath = 'documents/' . $document->id . '/' . $filename;
                            
                            $contents = file_get_contents($tempPath);
                            if ($contents !== false && Storage::disk('public')->put($storagePath, $contents)) {
                                $metadata[$fieldName] = $storagePath;
                            }
                        } catch (\Exception $e) {
                            \Log::error("Failed to upload dynamic file for doc index {$index}: " . $e->getMessage());
                        }
                    }
                    // Update metadata with the new file paths
                    $document->update(['metadata' => $metadata]);
                }
                
                // Automatically assign workflow based on template or legacy rules
                $this->workflowService->assignWorkflow($document);
            }

            return redirect()->route('documents.index')
                ->with('success', count($validated['documents']) . ' Dokumen pengajuan berhasil dibuat dan disimpan sebagai draft.');
        });
    }

    /**
     * Display the specified document.
     */
    public function show(Document $document)
    {
        $this->authorize('view', $document);

        $document->load(['user', 'attachments', 'approvals.user']);
        
        // Add debugging to see why actions aren't showing
        // \Log::info('Document Status: ' . $document->status);
        // \Log::info('Current Approver ID: ' . $document->current_approver_id);
        // \Log::info('Auth ID: ' . auth()->id());
        
        $canApprove = $document->status === 'pending' && $document->current_approver_id === auth()->id();
        $isCreator = $document->user_id === auth()->id();

        // Check if current user has permission to override approvals (e.g. Admin)
        $canOverride = auth()->user()->hasPermissionTo('manage-users') || auth()->user()->hasPermissionTo('view-all-documents');

        return view('documents.show', compact('document', 'canApprove', 'isCreator', 'canOverride'));
    }

    /**
     * Show the form for editing the specified document.
     */
    public function edit(Document $document)
    {
        $this->authorize('update', $document);

        // Only draft or revision requested documents can be edited
        if (!in_array($document->status, ['draft', 'revision_requested'])) {
            return redirect()->route('documents.show', $document)
                ->with('error', 'Hanya dokumen berstatus Draft atau Perlu Revisi yang dapat diedit.');
        }

        $typeAccess = config('workflow.type_access', []);
        $userDept = auth()->user()->department;
        $allowedTypes = [];

        foreach ($typeAccess as $type => $departments) {
            if (in_array('*', $departments) || in_array($userDept, $departments)) {
                $allowedTypes[] = $type;
            }
        }
        
        // Fetch active templates
        $templates = DocumentTemplate::active()
            ->whereIn('approval_type', $allowedTypes)
            ->get();

        return view('documents.edit', compact('document', 'templates'));
    }

    /**
     * Update the specified document in storage.
     */
    public function update(Request $request, Document $document)
    {
        $this->authorize('update', $document);

        if (!in_array($document->status, ['draft', 'revision_requested'])) {
            return redirect()->route('documents.show', $document)
                ->with('error', 'Dokumen tidak dapat diubah pada status saat ini.');
        }

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'approval_type' => 'required|in:only,po,invoice,loan',
            'po_category' => 'nullable|in:internal,external',
            'priority' => 'required|in:low,normal,high,urgent',
            'template' => 'nullable|string',
            // ... (keep rest of update rules as they were, flat structure for edit)
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
            'metadata' => 'nullable|array',
            'metadata_files' => 'nullable|array',
            'metadata_files.*' => 'nullable|file|max:10240',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ];

        $validated = $request->validate($rules);
        $this->sanitizeMoneyFields($request, ['subtotal', 'tax_amount', 'invoice_amount', 'loan_amount']);

        $metadata = $validated['metadata'] ?? []; // Start with dynamic metadata
        
        // Preserve existing dynamic file paths in metadata if not overwritten
        if (is_array($document->metadata)) {
            foreach ($document->metadata as $key => $value) {
                if (!isset($metadata[$key]) && is_string($value) && str_starts_with($value, 'documents/')) {
                    $metadata[$key] = $value;
                }
            }
        }

        switch ($validated['approval_type']) {
            case 'only':
                $metadata = array_merge($metadata, [
                    'document_category' => $request->input('document_category'),
                    'effective_date' => $request->input('effective_date'),
                    'expiry_date' => $request->input('expiry_date'),
                    'related_department' => $request->input('related_department'),
                ]);
                break;
            case 'po':
                $metadata = array_merge($metadata, [
                    'vendor_name' => $request->input('vendor_name'),
                    'vendor_npwp' => $request->input('vendor_npwp'),
                    'item_description' => $request->input('item_description'),
                    'subtotal' => $request->input('subtotal'),
                    'tax_amount' => $request->input('tax_amount'),
                    'budget_code' => $request->input('budget_code'),
                    'expected_delivery' => $request->input('expected_delivery'),
                ]);
                break;
            case 'invoice':
                $metadata = array_merge($metadata, [
                    'invoice_number' => $request->input('invoice_number'),
                    'invoice_date' => $request->input('invoice_date'),
                    'related_po_number' => $request->input('related_po_number'),
                    'vendor_name' => $request->input('vendor_name_invoice'),
                    'invoice_amount' => $request->input('invoice_amount'),
                    'payment_due_date' => $request->input('payment_due_date'),
                    'bank_account' => $request->input('bank_account'),
                    'tax_invoice' => $request->input('tax_invoice'),
                ]);
                break;
            case 'loan':
                $metadata = array_merge($metadata, [
                    'loan_type' => $request->input('loan_type'),
                    'loan_amount' => $request->input('loan_amount'),
                    'installment_count' => $request->input('installment_count'),
                    'repayment_start' => $request->input('repayment_start'),
                    'loan_reason' => $request->input('loan_reason'),
                ]);
                break;
        }

        $estimatedBudget = match ($validated['approval_type']) {
            'po' => ($request->input('subtotal', 0) + $request->input('tax_amount', 0)),
            'invoice' => $request->input('invoice_amount', 0) ?: 0,
            'loan' => $request->input('loan_amount', 0) ?: 0,
            default => null,
        };

        $document->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'approval_type' => $validated['approval_type'],
            'po_category' => $validated['po_category'] ?? 'external',
            'priority' => $validated['priority'],
            'estimated_budget' => $estimatedBudget ?: 0,
            'template' => $validated['template'] ?? null,
            'metadata' => $metadata,
        ]);

        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                try {
                    if (!$file || !($file instanceof \Illuminate\Http\UploadedFile) || !$file->isValid()) continue;
                    $tempPath = $file->getRealPath() ?: $file->getPathname() ?: $file->path();
                    if (empty($tempPath) || !file_exists($tempPath)) continue;

                    $extension = $file->getClientOriginalExtension();
                    $filename = uniqid() . '_' . time() . '.' . $extension;
                    $storagePath = 'documents/' . $document->id . '/' . $filename;
                    
                    $contents = file_get_contents($tempPath);
                    if ($contents === false) continue;

                    if (Storage::disk('public')->put($storagePath, $contents)) {
                        DocumentAttachment::create([
                            'document_id' => $document->id,
                            'file_name' => $file->getClientOriginalName(),
                            'file_path' => $storagePath,
                            'file_type' => $extension,
                            'file_size' => $file->getSize() ?: filesize($tempPath),
                            'uploaded_by' => auth()->id(),
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to upload file during update: ' . $e->getMessage());
                }
            }
        }

        // Handle dynamic file uploads on edit
        if ($request->hasFile('metadata_files')) {
            $dynamicFiles = $request->file('metadata_files');
            foreach ($dynamicFiles as $fieldName => $file) {
                try {
                    if (!$file || !($file instanceof \Illuminate\Http\UploadedFile) || !$file->isValid()) continue;
                    $tempPath = $file->getRealPath() ?: $file->getPathname() ?: $file->path();
                    if (empty($tempPath) || !file_exists($tempPath)) continue;

                    $extension = $file->getClientOriginalExtension();
                    $filename = uniqid() . '_df_' . time() . '.' . $extension;
                    $storagePath = 'documents/' . $document->id . '/' . $filename;
                    
                    $contents = file_get_contents($tempPath);
                    if ($contents !== false && Storage::disk('public')->put($storagePath, $contents)) {
                        $metadata[$fieldName] = $storagePath;
                    }
                } catch (\Exception $e) {
                    \Log::error("Failed to upload dynamic file during update: " . $e->getMessage());
                }
            }
            $document->update(['metadata' => $metadata]);
        }

        return redirect()->route('documents.show', $document)
            ->with('success', 'Dokumen berhasil diperbarui.');
    }

    /**
     * Submit a draft document for approval.
     */
    public function submit(Request $request, Document $document)
    {
        $this->authorize('update', $document);

        if (!in_array($document->status, ['draft', 'revision_requested'])) {
            return back()->with('error', 'Hanya dokumen berstatus Draft atau Perlu Revisi yang dapat disubmit.');
        }

        // Validate that workflow is assigned
        if (!$document->current_approver_id) {
            // Try to assign it now
            $this->workflowService->assignWorkflow($document);
            $document->refresh();

            if (!$document->current_approver_id) {
                return back()->with('error', 'Gagal submit: Tidak ada alur approval yang tersedia untuk tipe dokumen ini. Silakan hubungi Administrator.');
            }
        }

        $document->update([
            'status' => 'pending',
            'submitted_at' => now()
        ]);

        // Send notification to first approver
        try {
            $approver = \App\Models\User::find($document->current_approver_id);
            if ($approver) {
                $approver->notify(new DocumentNotification($document, 'requires_approval'));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send notification on submit: ' . $e->getMessage());
        }

        return redirect()->route('documents.show', $document)
            ->with('success', 'Dokumen berhasil disubmit untuk approval.');
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        if (!in_array($document->status, ['draft', 'rejected'])) {
            return back()->with('error', 'Hanya dokumen berstatus Draft atau Ditolak yang dapat dihapus.');
        }

        // Delete attachments from storage
        foreach ($document->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Dokumen berhasil dihapus.');
    }

    /**
     * Remove a specific attachment.
     */
    public function deleteAttachment(Document $document, DocumentAttachment $attachment)
    {
        $this->authorize('update', $document);

        if (!in_array($document->status, ['draft', 'revision_requested'])) {
            return response()->json(['message' => 'Tidak dapat menghapus lampiran pada status dokumen saat ini.'], 403);
        }

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return response()->json(['message' => 'Lampiran berhasil dihapus.']);
    }

    /**
     * Generate and stream PDF for the document.
     */
    public function print(Document $document)
    {
        $this->authorize('view', $document);

        $document->load(['user', 'approvals.user']);
        
        // Ensure QR Hash exists
        if (!$document->approval_hash) {
            $document->generateHash();
        }

        // Generate QR Code URL
        $verificationUrl = route('verify.show', $document->approval_hash);
        $qrCode = base64_encode(QrCode::format('svg')->size(100)->generate($verificationUrl));

        $pdf = Pdf::loadView('documents.pdf', compact('document', 'qrCode'));
        
        // Set paper size to A4
        $pdf->setPaper('A4', 'portrait');
        
        // Set filename
        $filename = 'DOC-' . $document->document_number . '.pdf';

        return $pdf->stream($filename);
    }
    
    /**
     * Add a comment to the document
     */
    public function comment(Request $request, Document $document)
    {
        $this->authorize('view', $document);
        
        $validated = $request->validate([
            'comment' => 'required|string|max:1000'
        ]);
        
        $document->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $validated['comment']
        ]);
        
        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    /**
     * Sanitize money fields by removing non-numeric characters (except minus sign).
     */
    protected function sanitizeMoneyFields(Request $request, array $fields): void
    {
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $value = $request->input($field);
                if (is_string($value)) {
                    // Remove dots, commas if used as thousands separator
                    $cleanValue = preg_replace('/[^\d-]/', '', $value);
                    $request->merge([$field => $cleanValue]);
                }
            }
        }
    }
}
