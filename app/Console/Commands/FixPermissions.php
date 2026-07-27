<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class FixPermissions extends Command
{
    protected $signature = 'app:fix-permissions {--dry-run : Show what would be changed without making changes}';

    protected $description = 'Normalize role names to lowercase, verify permissions, and reset Spatie cache';

    /**
     * All expected roles (lowercase)
     */
    private array $expectedRoles = [
        'admin',
        'staff',
        'supervisor',
        'manager',
        'hrd',
        'finance',
        'direktur_hr',
        'direktur_finance',
        'direktur_it',
        'direktur_sales',
        'presiden_direktur',
    ];

    /**
     * Permissions that should exist for all roles
     */
    private array $basePermissions = [
        'view-document',
        'create-document',
        'edit-document',
        'delete-document',
        'submit-document',
    ];

    /**
     * Permissions that should exist for approver roles
     */
    private array $approverPermissions = [
        'approve-document',
        'reject-document',
    ];

    /**
     * Roles that should have approver permissions
     */
    private array $approverRoles = [
        'supervisor',
        'manager',
        'hrd',
        'finance',
        'direktur_hr',
        'direktur_finance',
        'direktur_it',
        'direktur_sales',
        'presiden_direktur',
    ];

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
            $this->line('');
        }

        $this->normalizeRoleNames($isDryRun);
        $this->verifyPermissions($isDryRun);
        $this->checkGuardConsistency();
        $this->checkUserRoleAssignments();

        if (!$isDryRun) {
            $this->resetCache();
        }

        $this->line('');
        $this->info('✅ Permission check complete!');

        return Command::SUCCESS;
    }

    private function normalizeRoleNames(bool $isDryRun): void
    {
        $this->info('📋 Step 1: Checking role name case...');

        $roles = Role::all();
        $fixed = 0;

        foreach ($roles as $role) {
            $lowercase = strtolower($role->name);
            if ($role->name !== $lowercase) {
                $this->warn("   ⚠️  Role '{$role->name}' should be '{$lowercase}'");
                if (!$isDryRun) {
                    $role->update(['name' => $lowercase]);
                    $this->info("   ✅ Fixed: '{$role->name}' → '{$lowercase}'");
                }
                $fixed++;
            }
        }

        if ($fixed === 0) {
            $this->info('   ✅ All role names are already lowercase');
        } else {
            $this->info("   Found {$fixed} role(s) needing normalization");
        }
    }

    private function verifyPermissions(bool $isDryRun): void
    {
        $this->line('');
        $this->info('🔐 Step 2: Verifying role permissions...');

        foreach ($this->expectedRoles as $roleName) {
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                $this->error("   ❌ Role '{$roleName}' not found in database!");
                if (!$isDryRun) {
                    $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
                    $this->info("   ✅ Created role: {$roleName}");
                }
                continue;
            }

            // Check guard
            if ($role->guard_name !== 'web') {
                $this->warn("   ⚠️  Role '{$roleName}' has guard '{$role->guard_name}', expected 'web'");
                if (!$isDryRun) {
                    $role->update(['guard_name' => 'web']);
                    $this->info("   ✅ Fixed guard for role: {$roleName}");
                }
            }

            // Check base permissions
            $missingPerms = [];
            foreach ($this->basePermissions as $perm) {
                if (!$role->hasPermissionTo($perm)) {
                    $missingPerms[] = $perm;
                }
            }

            // Check approver permissions for approver roles
            if (in_array($roleName, $this->approverRoles)) {
                foreach ($this->approverPermissions as $perm) {
                    if (!$role->hasPermissionTo($perm)) {
                        $missingPerms[] = $perm;
                    }
                }
            }

            if (!empty($missingPerms)) {
                $this->warn("   ⚠️  Role '{$roleName}' missing: " . implode(', ', $missingPerms));
                if (!$isDryRun) {
                    foreach ($missingPerms as $perm) {
                        $permission = Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
                        $role->givePermissionTo($permission);
                    }
                    $this->info("   ✅ Granted missing permissions to: {$roleName}");
                }
            } else {
                $this->line("   ✅ {$roleName}: all permissions OK");
            }
        }
    }

    private function checkGuardConsistency(): void
    {
        $this->line('');
        $this->info('🛡️  Step 3: Checking guard consistency...');

        $nonWebRoles = Role::where('guard_name', '!=', 'web')->count();
        $nonWebPerms = Permission::where('guard_name', '!=', 'web')->count();

        if ($nonWebRoles > 0) {
            $this->error("   ❌ Found {$nonWebRoles} role(s) with non-web guard!");
            Role::where('guard_name', '!=', 'web')->each(function ($role) {
                $this->warn("      - {$role->name} (guard: {$role->guard_name})");
            });
        } else {
            $this->info('   ✅ All roles use "web" guard');
        }

        if ($nonWebPerms > 0) {
            $this->error("   ❌ Found {$nonWebPerms} permission(s) with non-web guard!");
        } else {
            $this->info('   ✅ All permissions use "web" guard');
        }
    }

    private function checkUserRoleAssignments(): void
    {
        $this->line('');
        $this->info('👥 Step 4: Checking user role assignments...');

        $usersWithoutRoles = \App\Models\User::whereDoesntHave('roles')->where('is_active', true)->get();

        if ($usersWithoutRoles->isEmpty()) {
            $this->info('   ✅ All active users have roles assigned');
        } else {
            $this->error("   ❌ Found {$usersWithoutRoles->count()} active user(s) without roles:");
            foreach ($usersWithoutRoles as $user) {
                $this->warn("      - {$user->name} ({$user->email})");
            }
        }

        // Show role distribution
        $this->line('');
        $this->info('   📊 Role Distribution:');
        $roles = Role::withCount('users')->get();
        foreach ($roles as $role) {
            $this->line("      {$role->name}: {$role->users_count} user(s)");
        }
    }

    private function resetCache(): void
    {
        $this->line('');
        $this->info('🔄 Step 5: Resetting Spatie permission cache...');

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->info('   ✅ Permission cache cleared');
    }
}
