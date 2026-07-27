<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Department extends Model
{
    protected $fillable = [
        'name',
        'code',
        'director_role',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ==================== CACHE ====================

    private static string $cacheKey = 'departments_all';
    private static int $cacheTTL = 300; // 5 minutes

    protected static function booted(): void
    {
        static::saved(fn() => Cache::forget(static::$cacheKey));
        static::deleted(fn() => Cache::forget(static::$cacheKey));
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ==================== STATIC HELPERS ====================

    /**
     * Get all active departments (cached).
     */
    public static function allActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(static::$cacheKey, static::$cacheTTL, function () {
            return static::active()->ordered()->get();
        });
    }

    /**
     * Get department names as simple array.
     */
    public static function namesList(): array
    {
        return static::allActive()->pluck('name')->toArray();
    }

    /**
     * Get department names for dropdown [name => name].
     */
    public static function dropdown(): array
    {
        return static::allActive()->pluck('name', 'name')->toArray();
    }

    /**
     * Get division directors mapping: [department_name => director_role].
     */
    public static function directorMap(): array
    {
        return static::allActive()
            ->whereNotNull('director_role')
            ->pluck('director_role', 'name')
            ->toArray();
    }

    /**
     * Find director role for a department name.
     */
    public static function directorRoleFor(?string $departmentName): ?string
    {
        if (!$departmentName) return null;

        $dept = static::allActive()->firstWhere('name', $departmentName);
        return $dept?->director_role;
    }
}
