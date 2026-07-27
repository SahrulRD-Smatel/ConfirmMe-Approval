<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = DocumentTemplate::with('creator')
            ->latest()
            ->paginate(20);

        return view('admin.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.templates.form');
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
            'fields.*' => 'nullable|string|max:2000',
        ]);

        // Clean empty fields
        $fields = collect($validated['fields'] ?? [])->filter(fn($v) => $v !== null && $v !== '')->toArray();

        DocumentTemplate::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'],
            'approval_type' => $validated['approval_type'],
            'fields' => $fields,
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
        return view('admin.templates.form', compact('template'));
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
            'fields.*' => 'nullable|string|max:2000',
        ]);

        // Clean empty fields
        $fields = collect($validated['fields'] ?? [])->filter(fn($v) => $v !== null && $v !== '')->toArray();

        $template->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'],
            'approval_type' => $validated['approval_type'],
            'fields' => $fields,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template berhasil diperbarui!');
    }

    public function destroy(DocumentTemplate $template)
    {
        $template->delete();

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template berhasil dihapus!');
    }
}
