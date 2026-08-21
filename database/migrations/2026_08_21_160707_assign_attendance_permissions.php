<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Permission::firstOrCreate(['name' => 'manage-attendance']);
        
        $hrd = Role::where('name', 'hrd')->first();
        if ($hrd) $hrd->givePermissionTo('manage-attendance');

        $hrdCoord = Role::where('name', 'hrd_coordinator')->first();
        if ($hrdCoord) $hrdCoord->givePermissionTo('manage-attendance');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 
    }
};
