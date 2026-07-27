<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Loan Fund Setting
 *
 * Pengaturan dana pinjaman perusahaan.
 * Digunakan untuk tracking ketersediaan dana pinjaman karyawan.
 */
class LoanFundSetting extends Model
{
    protected $fillable = [
        'fiscal_year',
        'total_fund',
        'used_fund',
        'reserved_fund',
        'max_loan_per_employee',
        'max_installment_months',
        'is_active',
        'notes',
        'updated_by',
    ];

    protected $casts = [
        'fiscal_year' => 'integer',
        'total_fund' => 'decimal:2',
        'used_fund' => 'decimal:2',
        'reserved_fund' => 'decimal:2',
        'max_loan_per_employee' => 'decimal:2',
        'max_installment_months' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get available fund
     */
    public function getAvailableFundAttribute(): float
    {
        return max(0, $this->total_fund - $this->used_fund - $this->reserved_fund);
    }

    /**
     * Check if fund is sufficient for a loan amount
     */
    public function hasSufficientFund(float $amount): bool
    {
        return $this->available_fund >= $amount;
    }

    /**
     * Reserve fund for a pending loan
     */
    public function reserveFund(float $amount): bool
    {
        if (!$this->hasSufficientFund($amount)) {
            return false;
        }
        $this->increment('reserved_fund', $amount);
        return true;
    }

    /**
     * Release reserved fund (when loan is cancelled/rejected)
     */
    public function releaseFund(float $amount): void
    {
        $this->decrement('reserved_fund', min($amount, $this->reserved_fund));
    }

    /**
     * Use fund (move from reserved to used - when loan is disbursed)
     */
    public function useFund(float $amount): void
    {
        $this->decrement('reserved_fund', min($amount, $this->reserved_fund));
        $this->increment('used_fund', $amount);
    }

    /**
     * Return fund (when loan repayment is recorded)
     */
    public function returnFund(float $amount): void
    {
        $this->decrement('used_fund', min($amount, $this->used_fund));
    }

    /**
     * Get active setting for current year
     */
    public static function current(): ?self
    {
        return static::where('fiscal_year', date('Y'))
            ->where('is_active', true)
            ->first();
    }

    /**
     * Scope for active settings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get usage percentage
     */
    public function getUsagePercentageAttribute(): float
    {
        if ($this->total_fund <= 0) return 0;
        return round(($this->used_fund / $this->total_fund) * 100, 1);
    }

    /**
     * Formatted amounts
     */
    public function getFormattedTotalFundAttribute(): string
    {
        return 'Rp ' . number_format($this->total_fund, 0, ',', '.');
    }

    public function getFormattedAvailableFundAttribute(): string
    {
        return 'Rp ' . number_format($this->available_fund, 0, ',', '.');
    }

    public function getFormattedMaxLoanAttribute(): string
    {
        return 'Rp ' . number_format($this->max_loan_per_employee, 0, ',', '.');
    }
}
