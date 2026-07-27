<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ApprovalThreshold extends Model
{
    protected $fillable = [
        'approval_type', 'label', 'min_amount', 'max_amount',
        'final_role', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get thresholds for a specific type.
     */
    public static function getForType(string $type): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('approval_type', $type)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Clear cache when thresholds change.
     */
    public static function clearCache(): void
    {
        Cache::forget('approval_thresholds_all');
    }

    protected static function booted(): void
    {
        static::saved(fn() => static::clearCache());
        static::deleted(fn() => static::clearCache());
    }
}
