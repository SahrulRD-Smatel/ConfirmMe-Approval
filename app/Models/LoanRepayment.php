<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRepayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_loan_id',
        'installment_number',
        'amount',
        'payment_date',
        'payment_method',
        'proof_file',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the employee loan
     */
    public function employeeLoan()
    {
        return $this->belongsTo(EmployeeLoan::class);
    }

    /**
     * Get who recorded this repayment
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // ==================== ACCESSORS ====================

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'salary_deduction' => 'Potong Gaji',
            'transfer' => 'Transfer Bank',
            'cash' => 'Tunai',
            default => ucfirst($this->payment_method ?? 'Lainnya'),
        };
    }
}
