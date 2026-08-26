<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Helper to group permissions dynamically
     */
    private function getGroupedPermissions()
    {
        $permissions = Permission::orderBy('name')->pluck('name');
        
        $groups = [];
        foreach ($permissions as $perm) {
            if (str_contains($perm, 'document')) $groups[__('Dokumen')][] = $perm;
            elseif (str_contains($perm, 'budget') || str_contains($perm, 'po') || str_contains($perm, 'invoice') || str_contains($perm, 'purchasing')) $groups[__('Keuangan')][] = $perm;
            elseif (str_contains($perm, 'attendance')) $groups[__('HR & Absensi')][] = $perm;
            elseif (str_contains($perm, 'reports')) $groups[__('Laporan')][] = $perm;
            elseif (str_contains($perm, 'users') || str_contains($perm, 'roles') || str_contains($perm, 'workflow') || str_contains($perm, 'templates') || str_contains($perm, 'features') || str_contains($perm, 'departments') || str_contains($perm, 'audit-log')) $groups[__('Administrasi')][] = $perm;
            else $groups[__('Lainnya')][] = $perm;
        }
        
        return $groups;
    }

    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $hierarchy = [
            'staff',
            'ga',
            'supervisor',
            'hrd_coordinator',
            'purchasing',
            'finance',
            'hrd',
            'manager_hrga',
            'manager',
            'direktur_operasional',
            'direktur_hr',
            'direktur_finance',
            'direktur_it',
            'direktur_sales',
            'direktur',
            'presiden_direktur',
            'admin'
        ];

        $roles = Role::with('permissions')->get()->sortBy(function($role) use ($hierarchy) {
            $pos = array_search(strtolower($role->name), $hierarchy);
            // Default position for custom roles not in hierarchy is 999
            return $pos === false ? 999 : $pos;
        });

        $permGroups = $this->getGroupedPermissions();

        return view('admin.roles.index', compact('roles', 'permGroups'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permGroups = $this->getGroupedPermissions();
        return view('admin.roles.form', compact('permGroups'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role berhasil dibuat!');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the role.
     */
    public function edit(Role $role)
    {
        $permGroups = $this->getGroupedPermissions();
        return view('admin.roles.form', compact('role', 'permGroups'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role berhasil diperbarui!');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of default roles
        $protectedRoles = ['admin', 'staff', 'supervisor', 'hrd', 'direktur', 'finance', 'purchasing', 'direktur_operasional', 'hrd_coordinator', 'manager', 'direktur_hr', 'direktur_finance', 'direktur_it', 'direktur_sales', 'presiden_direktur'];

        if (in_array($role->name, $protectedRoles)) {
            return back()->with('error', 'Role default tidak dapat dihapus!');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role berhasil dihapus!');
    }
}
