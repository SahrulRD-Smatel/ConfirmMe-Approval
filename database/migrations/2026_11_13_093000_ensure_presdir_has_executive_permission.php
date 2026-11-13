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
        // Ensure menu-executive-dashboard permission exists
        $perm = Permission::firstOrCreate(['name' => 'menu-executive-dashboard']);

        // Ensure presiden_direktur has this permission
        $presdir = Role::where('name', 'presiden_direktur')->first();
        if ($presdir && !$presdir->hasPermissionTo('menu-executive-dashboard')) {
            $presdir->givePermissionTo($perm);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op for safety
    }
};

