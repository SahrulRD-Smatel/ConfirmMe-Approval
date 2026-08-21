<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new dynamic permissions
        $permissions = [
            'view-all-documents',
            'view-department-documents',
            'view-finance-documents',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Give them to standard roles temporarily so we don't break existing access immediately
        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $admin->givePermissionTo('view-all-documents');
        }

        $manager = Role::where('name', 'manager')->first();
        if ($manager) {
            $manager->givePermissionTo('view-department-documents');
        }

        $finance = Role::where('name', 'finance')->first();
        if ($finance) {
            $finance->givePermissionTo('view-finance-documents');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::whereIn('name', [
            'view-all-documents',
            'view-department-documents',
            'view-finance-documents',
        ])->delete();
    }
};
