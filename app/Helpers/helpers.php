<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

if (!function_exists('role_label')) {
    /**
     * Convert a role slug to a human-readable label.
     * e.g. "direktur_finance" → "Direktur Finance"
     *      "hrd_coordinator"  → "HRD Coordinator"
     *      "presiden_direktur" → "Presiden Direktur"
     */
    function role_label(?string $role): string
    {
        if (!$role) return '-';

        // Special mappings for non-obvious names
        $labels = [
            'admin' => 'Administrator',
            'staff' => 'Staff',
            'supervisor' => 'Supervisor',
            'manager' => 'Manager',
            'hrd' => 'HRD',
            'hrd_coordinator' => 'HRD Coordinator',
            'finance' => 'Finance',
            'purchasing' => 'Purchasing',
            'direktur_hr' => 'Direktur HR',
            'direktur_finance' => 'Direktur Finance',
            'direktur_it' => 'Direktur IT',
            'direktur_sales' => 'Direktur Sales',
            'direktur_operasional' => 'Direktur Operasional',
            'presiden_direktur' => 'Presiden Direktur',
        ];

        return $labels[$role] ?? ucwords(str_replace('_', ' ', $role));
    }
}

if (!function_exists('feature_active')) {
    /**
     * Check if a system feature is active.
     *
     * @param string $key The feature key (e.g., 'module_finance')
     * @return bool
     */
    function feature_active(string $key): bool
    {
        return Cache::remember("system_feature_{$key}", 3600, function () use ($key) {
            $feature = DB::table('system_features')->where('key', $key)->first();
            
            // If feature doesn't exist, assume it's active by default to prevent breaking
            if (!$feature) {
                return true;
            }
            
            return (bool) $feature->is_active;
        });
    }
}
