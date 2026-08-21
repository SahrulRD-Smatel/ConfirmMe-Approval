<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApprovalThreshold;
use App\Models\WorkflowChain;
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
        $workflowTypes = WorkflowChain::getAllChains();

        // Fallback to config if DB is empty
        if (empty($workflowTypes)) {
            $workflowTypes = config('workflow.types', []);
        }

        $thresholds = ApprovalThreshold::orderBy('approval_type')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('approval_type');

        $typeAccess = WorkflowSetting::getTypeAccess();
        $roleHierarchy = WorkflowSetting::getRoleHierarchy();
        $functionalRoles = WorkflowSetting::getFunctionalRoles();
        $hrdKeywords = WorkflowSetting::getHrdKeywords();
        $departments = \App\Models\Department::ordered()->pluck('name')->toArray();
        $allRoles = Role::orderBy('name')->pluck('name')->toArray();

        $roleLabels = config('workflow.role_labels', []);
        $typeLabels = config('workflow.type_labels', []);

        return view('admin.workflow.index', compact(
            'workflowTypes',
            'thresholds',
            'typeAccess',
            'roleHierarchy',
            'functionalRoles',
            'hrdKeywords',
            'departments',
            'allRoles',
            'roleLabels',
            'typeLabels'
        ));
    }

    /**
     * Update type access configuration.
     */
    public function updateTypeAccess(Request $request)
    {
        $validated = $request->validate([
            'type_access' => 'required|array',
            'type_access.*' => 'array',
        ]);

        // Convert checkbox data to the expected format
        $typeAccess = [];
        foreach ($validated['type_access'] as $type => $depts) {
            if (in_array('*', $depts)) {
                $typeAccess[$type] = ['*'];
            } else {
                $typeAccess[$type] = array_values(array_filter($depts));
            }
        }

        WorkflowSetting::set('type_access', $typeAccess);

        return back()->with('success', 'Pengaturan akses tipe berhasil disimpan!');
    }

    /**
     * Update general settings (role hierarchy, functional roles, HRD keywords).
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

            case 'hrd_keywords':
                $validated = $request->validate([
                    'keywords' => 'required|string',
                ]);
                $keywords = array_values(array_filter(
                    array_map('trim', explode(',', $validated['keywords']))
                ));
                WorkflowSetting::set('hrd_keywords', $keywords);
                return back()->with('success', 'HRD keywords berhasil disimpan!');

            // Removed departments case as it's now managed via Department CRUD

            default:
                return back()->with('error', 'Section tidak dikenali.');
        }
    }

    /**
     * Reset all workflow settings to default from config.
     */
    public function resetToDefault(Request $request)
    {
        $type = $request->input('type');

        if ($type === 'all' || $type === 'chains') {
            // Reset chains from config
            $configChains = config('workflow.types', []);
            foreach ($configChains as $approvalType => $steps) {
                WorkflowChain::saveChain($approvalType, array_values($steps));
            }
        }

        if ($type === 'all' || $type === 'settings') {
            // Reset settings
            WorkflowSetting::set('role_hierarchy', [
                'staff' => 1, 'supervisor' => 2, 'purchasing' => 2, 'hrd_coordinator' => 2,
                'manager' => 3, 'hrd' => 3, 'finance' => 3,
                'direktur' => 4, 'direktur_hr' => 4, 'direktur_finance' => 4,
                'direktur_it' => 4, 'direktur_sales' => 4, 'direktur_operasional' => 4,
                'presiden_direktur' => 5,
            ]);
            WorkflowSetting::set('functional_roles', ['finance', 'hrd', 'purchasing']);
        }

        return back()->with('success', 'Konfigurasi berhasil direset ke default!');
    }
}
