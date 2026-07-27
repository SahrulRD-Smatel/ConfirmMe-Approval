<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class WorkflowChain extends Model
{
    protected $fillable = ['approval_type', 'role', 'step_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'step_order' => 'integer',
    ];

    /**
     * Get the ordered chain for a specific approval type.
     * Returns array like [1 => 'supervisor', 2 => 'hrd', ...]
     */
    public static function getChainForType(string $type): array
    {
        return Cache::remember("workflow_chain_{$type}", 300, function () use ($type) {
            return static::where('approval_type', $type)
                ->where('is_active', true)
                ->orderBy('step_order')
                ->pluck('role', 'step_order')
                ->toArray();
        });
    }

    /**
     * Get all chains grouped by type.
     */
    public static function getAllChains(): array
    {
        return Cache::remember('workflow_chains_all', 300, function () {
            $chains = static::where('is_active', true)
                ->orderBy('approval_type')
                ->orderBy('step_order')
                ->get();

            $result = [];
            foreach ($chains as $chain) {
                $result[$chain->approval_type][$chain->step_order] = $chain->role;
            }
            return $result;
        });
    }

    /**
     * Save a complete chain for a type (replace all steps).
     */
    public static function saveChain(string $type, array $roles): void
    {
        // Delete existing steps for this type
        static::where('approval_type', $type)->delete();

        // Insert new steps
        $order = 1;
        $seen = [];
        foreach ($roles as $role) {
            $role = trim($role);
            if (empty($role) || in_array($role, $seen)) {
                continue; // Skip empty or duplicate roles
            }
            $seen[] = $role;

            static::create([
                'approval_type' => $type,
                'role' => $role,
                'step_order' => $order,
                'is_active' => true,
            ]);
            $order++;
        }

        // Clear cache
        Cache::forget("workflow_chain_{$type}");
        Cache::forget('workflow_chains_all');
    }

    /**
     * Clear all workflow caches.
     */
    public static function clearCache(): void
    {
        $types = static::distinct()->pluck('approval_type');
        foreach ($types as $type) {
            Cache::forget("workflow_chain_{$type}");
        }
        Cache::forget('workflow_chains_all');
    }
}
