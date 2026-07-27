<?php

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
