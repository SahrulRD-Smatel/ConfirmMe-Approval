<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class WorkflowSetting extends Model
{
    protected $fillable = ['key', 'value', 'description'];

    protected $casts = [
        'value' => 'array',
    ];

    /**
     * Get a setting value by key, with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("workflow_setting_{$key}", 300, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value, ?string $description = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            array_filter([
                'value' => $value,
                'description' => $description,
            ], fn($v) => $v !== null)
        );

        Cache::forget("workflow_setting_{$key}");
    }

    /**
     * Get role hierarchy from DB, fallback to config/hardcoded.
     */
    public static function getRoleHierarchy(): array
    {
        return static::get('role_hierarchy', [
            'staff' => 1,
            'supervisor' => 2,
            'purchasing' => 2,
            'hrd_coordinator' => 2,
            'manager' => 3,
            'hrd' => 3,
            'finance' => 3,
            'direktur' => 4,
            'direktur_hr' => 4,
            'direktur_finance' => 4,
            'direktur_it' => 4,
            'direktur_sales' => 4,
            'direktur_operasional' => 4,
            'presiden_direktur' => 5,
        ]);
    }

    /**
     * Get functional roles (roles that should NOT be skipped by auto-skip).
     */
    public static function getFunctionalRoles(): array
    {
        return static::get('functional_roles', [
            'finance', 'hrd', 'purchasing',
            'direktur_sales', 'direktur_it', 'direktur_operasional',
            'direktur_finance', 'direktur_hr', 'presiden_direktur',
        ]);
    }

    /**
     * Get HRD keywords for conditional HRD approval.
     */
    public static function getHrdKeywords(): array
    {
        return static::get('hrd_keywords', [
            'karyawan', 'pegawai', 'sdm', 'hrd', 'rekrutmen', 'phk',
            'cuti', 'izin', 'lembur', 'gaji', 'tunjangan', 'training',
            'pelatihan', 'mutasi', 'promosi', 'demosi', 'kontrak kerja',
        ]);
    }

    /**
     * Get type access configuration.
     */
    public static function getTypeAccess(): array
    {
        return static::get('type_access', [
            'only' => ['*'],
            'po' => ['*'],
            'po_internal' => ['*'],
            'invoice' => ['Finance'],
            'loan' => ['*'],
        ]);
    }

    /**
     * Get departments list.
     */
    public static function getDepartments(): array
    {
        return static::get('departments', [
            'IT', 'Human Resources', 'Finance', 'Sales', 'Operations',
        ]);
    }
}
