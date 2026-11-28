<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkflowSetting;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class WorkflowController extends Controller
{
    /**
     * Display workflow configuration dashboard.
     */
    public function index()
    {
        $roleHierarchy = WorkflowSetting::getRoleHierarchy();
        $functionalRoles = WorkflowSetting::getFunctionalRoles();
        $allRoles = Role::orderBy('name')->pluck('name')->toArray();
        $roleLabels = config('workflow.role_labels', []);

        return view('admin.workflow.index', compact(
            'roleHierarchy',
            'functionalRoles',
            'allRoles',
            'roleLabels'
        ));
    }

    /**
     * Update general settings (role hierarchy, functional roles).
     */
    public function updateSettings(Request $request)
    {
        $section = $request->input('section');

        switch ($section) {
            case 'role_hierarchy':
                $validated = $request->validate([
                    'hierarchy' => 'required|array',
                    'hierarchy.*' => 'required|integer|min:1|max:10',
                ]);
                WorkflowSetting::set('role_hierarchy', $validated['hierarchy']);
                return back()->with('success', 'Role hierarchy berhasil disimpan!');

            case 'functional_roles':
                $validated = $request->validate([
                    'functional_roles' => 'array',
                    'functional_roles.*' => 'string',
                ]);
                WorkflowSetting::set('functional_roles', $validated['functional_roles'] ?? []);
                return back()->with('success', 'Functional roles berhasil disimpan!');

            default:
                return back()->with('error', 'Section tidak dikenali.');
        }
    }

    /**
     * Reset all workflow settings to default from config.
     */
    public function resetToDefault(Request $request)
    {
        WorkflowSetting::set('role_hierarchy', [
            'staff' => 1, 'supervisor' => 2, 'purchasing' => 2, 'hrd_coordinator' => 2,
            'manager' => 3, 'hrd' => 3, 'finance' => 3,
            'direktur' => 4, 'direktur_hr' => 4, 'direktur_finance' => 4,
            'direktur_it' => 4, 'direktur_sales' => 4, 'direktur_operasional' => 4,
            'presiden_direktur' => 5,
        ]);
        WorkflowSetting::set('functional_roles', ['finance', 'hrd', 'purchasing']);

        return back()->with('success', 'Konfigurasi hierarki berhasil direset ke default!');
    }
}
