<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use App\Services\ApprovalWorkflowService;

class TemplateController extends Controller
{
    protected $workflowService;

    public function __construct(ApprovalWorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    public function index()
    {
        $templates = DocumentTemplate::with('creator')
            ->latest()
            ->paginate(20);

        return view('admin.templates.index', compact('templates'));
    }

    public function create()
    {
        $roles = \Spatie\Permission\Models\Role::where('name', '!=', 'admin')->get();
        $users = \App\Models\User::with('roles')->where('is_active', true)->get();
        return view('admin.templates.form', compact('roles', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:document_templates,code',
            'description' => 'nullable|string',
            'approval_type' => 'required|in:only,po,invoice,loan',
            'is_active' => 'boolean',
            'fields' => 'nullable|array',
            'fields.*' => 'nullable|string|max:10000',
            'workflow' => 'required|array|min:1',
            'workflow.*' => 'required|string',
        ]);

        // Clean empty fields
        $fields = collect($validated['fields'] ?? [])->filter(fn($v) => $v !== null && $v !== '')->toArray();

        DocumentTemplate::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'],
            'approval_type' => $validated['approval_type'],
            'fields' => $fields,
            'workflow' => $validated['workflow'],
            'is_active' => $validated['is_active'] ?? true,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template berhasil dibuat!');
    }

    public function show(DocumentTemplate $template)
    {
        return view('admin.templates.show', compact('template'));
    }

    public function edit(DocumentTemplate $template)
    {
        $roles = \Spatie\Permission\Models\Role::where('name', '!=', 'admin')->get();
        $users = \App\Models\User::with('roles')->where('is_active', true)->get();
        return view('admin.templates.form', compact('template', 'roles', 'users'));
    }

    public function update(Request $request, DocumentTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:document_templates,code,' . $template->id,
            'description' => 'nullable|string',
            'approval_type' => 'required|in:only,po,invoice,loan',
            'is_active' => 'boolean',
            'fields' => 'nullable|array',
            'fields.*' => 'nullable|string|max:10000',
            'workflow' => 'required|array|min:1',
            'workflow.*' => 'required|string',
        ]);

        // Clean empty fields
        $fields = collect($validated['fields'] ?? [])->filter(fn($v) => $v !== null && $v !== '')->toArray();

        // Check if workflow changed or became inactive
        $workflowChanged = $template->workflow !== $validated['workflow'];
        $becameInactive = $template->is_active && !($validated['is_active'] ?? true);

        $template->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'],
            'approval_type' => $validated['approval_type'],
            'fields' => $fields,
            'workflow' => $validated['workflow'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if ($workflowChanged || $becameInactive) {
            $reason = $workflowChanged ? "Sistem: Susunan approver pada template telah diubah oleh Admin." : "Sistem: Template dokumen ini telah dinonaktifkan oleh Admin.";
            $this->workflowService->autoRejectPendingDocumentsForTemplate($template->code, $reason);
        }

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template berhasil diperbarui!');
    }

    public function destroy(DocumentTemplate $template)
    {
        $code = $template->code;
        $template->delete();
        
        $this->workflowService->autoRejectPendingDocumentsForTemplate($code, "Sistem: Template dokumen ini telah dihapus oleh Admin.");

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template berhasil dihapus dan dokumen terkait yang masih pending telah di-reject otomatis!');
    }
}
