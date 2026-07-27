<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'department',
        'fiscal_year',
        'total_amount',
        'used_amount',
        'reserved_amount',
        'committed_budget',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'fiscal_year' => 'integer',
        'total_amount' => 'decimal:2',
        'used_amount' => 'decimal:2',
        'reserved_amount' => 'decimal:2',
        'committed_budget' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get available budget (remaining = total - used - committed)
     */
    public function getAvailableAmountAttribute(): float
    {
        return $this->total_amount - $this->used_amount - $this->committed_budget;
    }

    /**
     * Get usage percentage
     */
    public function getUsagePercentageAttribute(): float
    {
        if ($this->total_amount <= 0) {
            return 0;
        }
        return round(($this->used_amount / $this->total_amount) * 100, 2);
    }

    /**
     * Check if budget has sufficient funds
     */
    public function hasSufficientFunds(float $amount): bool
    {
        return $this->available_amount >= $amount;
    }

    /**
     * Reserve amount
     */
    public function reserve(float $amount): bool
    {
        if (!$this->hasSufficientFunds($amount)) {
            return false;
        }

        $this->increment('reserved_amount', $amount);
        return true;
    }

    /**
     * Release reserved amount
     */
    public function releaseReserved(float $amount): void
    {
        $this->decrement('reserved_amount', min($amount, $this->reserved_amount));
    }

    /**
     * Use budget (move from reserved to used)
     */
    public function use(float $amount): void
    {
        $this->decrement('reserved_amount', min($amount, $this->reserved_amount));
        $this->increment('used_amount', $amount);
    }

    /**
     * Scope for active budgets
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by fiscal year
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('fiscal_year', $year);
    }

    /**
     * Scope by department
     */
    public function scopeForDepartment($query, string $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Get formatted amounts
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getFormattedUsedAttribute(): string
    {
        return 'Rp ' . number_format($this->used_amount, 0, ',', '.');
    }

    public function getFormattedAvailableAttribute(): string
    {
        return 'Rp ' . number_format($this->available_amount, 0, ',', '.');
    }
}
