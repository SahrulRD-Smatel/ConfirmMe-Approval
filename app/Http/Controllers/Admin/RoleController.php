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
            $data = [
                'name' => $perm,
                'label' => self::getPermissionLabel($perm)['label'],
                'type' => self::getPermissionLabel($perm)['type'],
            ];

            if (str_contains($perm, 'document') || str_contains($perm, 'menu-archive') || str_contains($perm, 'menu-team-documents') || str_contains($perm, 'menu-bulk-approval')) {
                $groups[__('Dokumen')][] = $data;
            } elseif (str_contains($perm, 'budget') || str_contains($perm, 'po') || str_contains($perm, 'invoice') || str_contains($perm, 'purchasing') || str_contains($perm, 'menu-my-loans')) {
                $groups[__('Keuangan')][] = $data;
            } elseif (str_contains($perm, 'attendance') || str_contains($perm, 'menu-my-leaves') || str_contains($perm, 'menu-office-locations') || str_contains($perm, 'menu-announcements')) {
                $groups[__('HR & Absensi')][] = $data;
            } elseif (str_contains($perm, 'reports')) {
                $groups[__('Laporan')][] = $data;
            } elseif (str_contains($perm, 'users') || str_contains($perm, 'roles') || str_contains($perm, 'workflow') || str_contains($perm, 'templates') || str_contains($perm, 'features') || str_contains($perm, 'departments') || str_contains($perm, 'audit-log') || str_contains($perm, 'menu-executive-dashboard')) {
                $groups[__('Administrasi')][] = $data;
            } else {
                $groups[__('Lainnya')][] = $data;
            }
        }
        
        return $groups;
    }

    public static function getPermissionLabel(string $perm): array
    {
        $labels = [
            'menu-bulk-approval' => ['type' => 'MENU', 'label' => 'Bulk Approval'],
            'menu-office-locations' => ['type' => 'MENU', 'label' => 'Lokasi Kantor'],
            'menu-announcements' => ['type' => 'MENU', 'label' => 'Pengumuman'],
            'menu-executive-dashboard' => ['type' => 'MENU', 'label' => 'Executive Dashboard'],
            'menu-my-loans' => ['type' => 'MENU', 'label' => 'Pinjaman Saya'],
            'menu-my-leaves' => ['type' => 'MENU', 'label' => 'Pengajuan Cuti'],
            'menu-archive' => ['type' => 'MENU', 'label' => 'Arsip Dokumen'],
            'menu-team-documents' => ['type' => 'MENU', 'label' => 'Dokumen Tim'],

            'create-document' => ['type' => 'FITUR', 'label' => 'Buat & Edit Pengajuan'],
            'view-document' => ['type' => 'FITUR', 'label' => 'Lihat Daftar Pengajuan'],
            'edit-document' => ['type' => 'FITUR', 'label' => 'Edit Dokumen'],
            'submit-document' => ['type' => 'FITUR', 'label' => 'Submit Dokumen'],
            'approve-document' => ['type' => 'FITUR', 'label' => 'Setujui Dokumen (Semua Tipe)'],
            'reject-document' => ['type' => 'FITUR', 'label' => 'Tolak Dokumen'],
            'print-document' => ['type' => 'FITUR', 'label' => 'Cetak Dokumen'],
            'delete-document' => ['type' => 'FITUR', 'label' => 'Hapus Dokumen'],
            'view-all-documents' => ['type' => 'FITUR', 'label' => 'Lihat Semua Dokumen Perusahaan'],
            'view-department-documents' => ['type' => 'FITUR', 'label' => 'Lihat Semua Dokumen Departemen'],
            'view-finance-documents' => ['type' => 'FITUR', 'label' => 'Lihat Semua Dokumen Keuangan'],
            
            'manage-budget' => ['type' => 'MENU', 'label' => 'Manajemen Anggaran'],
            'create-po' => ['type' => 'MENU', 'label' => 'Purchase Orders'],
            'create-invoice' => ['type' => 'MENU', 'label' => 'Invoices'],
            'manage-purchasing' => ['type' => 'FITUR', 'label' => 'Kelola Pembelian'],
            'print-po' => ['type' => 'FITUR', 'label' => 'Cetak PO'],
            'print-invoice' => ['type' => 'FITUR', 'label' => 'Cetak Invoice'],
            
            'manage-attendance' => ['type' => 'FITUR', 'label' => 'Kelola Data Absensi'],
            'view-attendance' => ['type' => 'FITUR', 'label' => 'Lihat Laporan Absensi'],
            
            'manage-users' => ['type' => 'MENU', 'label' => 'Manajemen User'],
            'manage-roles' => ['type' => 'MENU', 'label' => 'Role & Permissions'],
            'manage-features' => ['type' => 'MENU', 'label' => 'Fitur & Modul'],
            'manage-workflow' => ['type' => 'MENU', 'label' => 'Konfigurasi Workflow'],
            'manage-departments' => ['type' => 'MENU', 'label' => 'Departemen'],
            'manage-templates' => ['type' => 'MENU', 'label' => 'Template Dokumen'],
            'view-audit-log' => ['type' => 'MENU', 'label' => 'Audit Logs'],
            
            'view-reports' => ['type' => 'MENU', 'label' => 'Laporan Utama'],
            'export-reports' => ['type' => 'FITUR', 'label' => 'Export Laporan (PDF/Excel)'],
        ];

        return $labels[$perm] ?? ['type' => 'FITUR', 'label' => ucwords(str_replace('-', ' ', $perm))];
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
