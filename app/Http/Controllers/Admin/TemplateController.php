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
            'workflow' => 'required|array|min:1',
            'workflow.*' => 'required|string',
        ]);

        $workflow = array_values(array_filter($validated['workflow'] ?? [], fn($w) => !empty($w)));
        if (empty($workflow)) {
            return back()->withErrors(['workflow' => __('Harap tentukan minimal satu approver pada susunan workflow.')])->withInput();
        }

        $fields = $request->input('fields', []);
        if (isset($fields['dynamic_fields']) && is_array($fields['dynamic_fields'])) {
            $fields['dynamic_fields'] = array_values(array_filter($fields['dynamic_fields'], function ($df) {
                return !empty($df['name']) || !empty($df['label']);
            }));
        }

        DocumentTemplate::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'],
            'approval_type' => $validated['approval_type'],
            'fields' => $fields,
            'workflow' => $workflow,
            'is_active' => $validated['is_active'] ?? true,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.templates.index')
            ->with('success', __('Template berhasil dibuat!'));
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
            'workflow' => 'required|array|min:1',
            'workflow.*' => 'required|string',
        ]);

        $workflow = array_values(array_filter($validated['workflow'] ?? [], fn($w) => !empty($w)));
        if (empty($workflow)) {
            return back()->withErrors(['workflow' => __('Harap tentukan minimal satu approver pada susunan workflow.')])->withInput();
        }

        $fields = $request->input('fields', []);
        if (isset($fields['dynamic_fields']) && is_array($fields['dynamic_fields'])) {
            $fields['dynamic_fields'] = array_values(array_filter($fields['dynamic_fields'], function ($df) {
                return !empty($df['name']) || !empty($df['label']);
            }));
        }

        // Check if workflow changed or became inactive
        $oldWorkflow = array_values((array) ($template->workflow ?? []));
        $workflowChanged = $oldWorkflow !== $workflow;
        $becameInactive = $template->is_active && !($validated['is_active'] ?? true);

        $template->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'],
            'approval_type' => $validated['approval_type'],
            'fields' => $fields,
            'workflow' => $workflow,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if ($workflowChanged || $becameInactive) {
            $reason = $workflowChanged ? "Sistem: Susunan approver pada template telah diubah oleh Admin." : "Sistem: Template dokumen ini telah dinonaktifkan oleh Admin.";
            $this->workflowService->autoRejectPendingDocumentsForTemplate($template->code, $reason);
        }

        return redirect()->route('admin.templates.index')
            ->with('success', __('Template berhasil diperbarui!'));
    }

    public function duplicate(DocumentTemplate $template)
    {
        $newTemplate = $template->replicate();
        $newTemplate->name = $template->name . ' (Salinan)';
        
        $baseCode = $template->code . '_COPY';
        $code = $baseCode;
        $counter = 1;
        while (DocumentTemplate::where('code', $code)->exists()) {
            $code = $baseCode . '_' . $counter++;
        }
        $newTemplate->code = $code;
        $newTemplate->created_by = auth()->id();
        $newTemplate->save();

        return redirect()->route('admin.templates.edit', $newTemplate)
            ->with('success', __('Template berhasil diduplikasi! Silakan sesuaikan konfigurasinya.'));
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
