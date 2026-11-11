<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

if (!function_exists('format_acronyms')) {
    /**
     * Capitalize common business, organizational, and technical acronyms/abbreviations.
     * e.g. "Manager Hrga"  → "Manager HRGA"
     *      "Staff Ga"      → "Staff GA"
     *      "Atk"           → "ATK"
     *      "P3k"           → "P3K"
     *      "Direktur It"   → "Direktur IT"
     *      "Foto Sim A"    → "Foto SIM A"
     */
    function format_acronyms(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        static $acronyms = [
            'HRGA',
            'HRD',
            'GA',
            'HR',
            'IT',
            'SPV',
            'SOP',
            'KPI',
            'ATK',
            'P3K',
            'PO',
            'DO',
            'PR',
            'RFQ',
            'BAST',
            'SPK',
            'MOU',
            'SLA',
            'SIM',
            'KTP',
            'NPWP',
            'NIP',
            'NIK',
            'BPJS',
            'SK',
            'STNK',
            'BPKB',
            'PDF',
            'QR',
            'URL',
            'IP',
            'UI',
            'UX',
            'WIB',
            'WITA',
            'WIT',
            'IDR',
            'USD',
            'PPN',
            'PPH',
            'GMH',
            'ODC',
            'CEO',
            'CFO',
            'COO',
            'CTO',
            'HQ',
            'SIG',
            'ADM',
            'OPS',
            'GM',
        ];

        static $regex = null;
        if ($regex === null) {
            usort($acronyms, function ($a, $b) {
                $diff = strlen($b) - strlen($a);
                return $diff !== 0 ? $diff : strcmp($a, $b);
            });
            $pattern = implode('|', array_map(fn($a) => preg_quote($a, '/'), $acronyms));
            $regex = '/\b(' . $pattern . ')\b/i';
        }

        return preg_replace_callback($regex, function ($matches) {
            return strtoupper($matches[0]);
        }, $text);
    }
}

if (!function_exists('role_label')) {
    /**
     * Convert a role slug to a human-readable label.
     * e.g. "direktur_finance" → "Direktur Finance"
     *      "manager_hrga"     → "Manager HRGA"
     *      "ga"               → "Staff GA"
     *      "hrd_coordinator"  → "HRD Coordinator"
     *      "presiden_direktur" → "Presiden Direktur"
     */
    function role_label(?string $role): string
    {
        if (!$role) return '-';

        $role = preg_replace('/^(role:|user:)/', '', trim($role));

        // Use config role_labels if defined
        $configLabels = function_exists('config') ? config('workflow.role_labels', []) : [];
        if (isset($configLabels[$role])) {
            return format_acronyms($configLabels[$role]);
        }

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
            'ga' => 'Staff GA',
            'staff_ga' => 'Staff GA',
            'manager_hrga' => 'Manager HRGA',
            'hrga' => 'HRGA',
            'admin_operasional' => 'Admin Operasional',
            'it_internal' => 'IT Internal',
            'staff_it' => 'Staff IT',
            'it_spv' => 'IT SPV',
            'spv_it' => 'IT SPV',
            'spv' => 'SPV',
        ];

        $label = $labels[$role] ?? ucwords(str_replace('_', ' ', $role));
        return format_acronyms($label);
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
