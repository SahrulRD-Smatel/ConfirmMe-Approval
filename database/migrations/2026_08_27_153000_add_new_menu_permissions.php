<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Define the new menu permissions
        $permissions = [
            'menu-bulk-approval',
            'menu-office-locations',
            'menu-announcements',
            'menu-executive-dashboard',
            'menu-my-loans',
            'menu-my-leaves',
            'menu-archive',
            'menu-team-documents',
        ];

        // Insert new permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // We should also assign them to the roles that implicitly had them before.
        // This ensures nobody loses access immediately after the migration runs.
        
        // 1. Bulk Approval: Everyone except presiden_direktur had it (implicitly via approve-document)
        $rolesWithApprove = Role::whereHas('permissions', function($q) {
            $q->where('name', 'approve-document');
        })->where('name', '!=', 'presiden_direktur')->get();
        
        foreach ($rolesWithApprove as $role) {
            $role->givePermissionTo('menu-bulk-approval');
            $role->givePermissionTo('menu-team-documents');
        }

        // 2. Executive Dashboard: Only presiden_direktur
        $presdir = Role::where('name', 'presiden_direktur')->first();
        if ($presdir) {
            $presdir->givePermissionTo('menu-executive-dashboard');
        }

        // 3. Office Locations: admin, hrd, direktur_hr
        $officeRoles = Role::whereIn('name', ['admin', 'hrd', 'direktur_hr'])->get();
        foreach ($officeRoles as $role) {
            $role->givePermissionTo('menu-office-locations');
        }

        // 4. Announcements: Anyone with manage-attendance (and Human Resources department, but we can't map dept to role easily here, so just manage-attendance for now)
        $hrRoles = Role::whereHas('permissions', function($q) {
            $q->where('name', 'manage-attendance');
        })->get();
        foreach ($hrRoles as $role) {
            $role->givePermissionTo('menu-announcements');
        }

        // 5. Pinjaman Saya & Cuti: Anyone with create-document EXCEPT admin
        $staffRoles = Role::whereHas('permissions', function($q) {
            $q->where('name', 'create-document');
        })->where('name', '!=', 'admin')->get();
        foreach ($staffRoles as $role) {
            $role->givePermissionTo('menu-my-loans');
            $role->givePermissionTo('menu-my-leaves');
        }

        // 6. Archive: Anyone with view-document EXCEPT presiden_direktur
        $viewRoles = Role::whereHas('permissions', function($q) {
            $q->where('name', 'view-document');
        })->where('name', '!=', 'presiden_direktur')->get();
        foreach ($viewRoles as $role) {
            $role->givePermissionTo('menu-archive');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'menu-bulk-approval',
            'menu-office-locations',
            'menu-announcements',
            'menu-executive-dashboard',
            'menu-my-loans',
            'menu-my-leaves',
            'menu-archive',
            'menu-team-documents',
        ];

        foreach ($permissions as $permission) {
            $perm = Permission::where('name', $permission)->first();
            if ($perm) {
                $perm->delete();
            }
        }
    }
};
