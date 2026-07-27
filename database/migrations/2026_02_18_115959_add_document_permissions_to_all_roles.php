<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration {
    /**
     * Run the migrations.
     * Adds document creation/editing permissions to all roles so that
     * supervisors, managers, directors, etc. can also create documents.
     */
    public function up(): void
    {
        // Ensure permissions exist
        $docPermissions = [
            'create-document',
            'edit-document',
            'delete-document',
            'submit-document',
        ];

        foreach ($docPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Roles that need document creation permissions
        $roles = [
            'supervisor',
            'manager',
            'hrd',
            'direktur_hr',
            'direktur_finance',
            'direktur_it',
            'direktur_sales',
            'presiden_direktur',
            'finance',
        ];

        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role) {
                $role->givePermissionTo($docPermissions);
            }
        }

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $docPermissions = [
            'create-document',
            'edit-document',
            'delete-document',
            'submit-document',
        ];

        $roles = [
            'supervisor',
            'manager',
            'hrd',
            'direktur_hr',
            'direktur_finance',
            'direktur_it',
            'direktur_sales',
            'presiden_direktur',
            'finance',
        ];

        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role) {
                $role->revokePermissionTo($docPermissions);
            }
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
