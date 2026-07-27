<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeLoan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_number',
        'document_id',
        'employee_id',
        'loan_amount',
        'installment_months',
        'monthly_installment',
        'last_installment',
        'total_paid',
        'outstanding_amount',
        'status',
        'disbursement_date',
        'disbursement_proof',
        'bank_account_name',
        'bank_account_number',
        'bank_name',
        'notes',
        'disbursed_by',
        'created_by',
    ];

    protected $casts = [
        'loan_amount' => 'decimal:2',
        'monthly_installment' => 'decimal:2',
        'last_installment' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'outstanding_amount' => 'decimal:2',
        'disbursement_date' => 'date',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($loan) {
            if (empty($loan->loan_number)) {
                $loan->loan_number = self::generateLoanNumber();
            }
        });
    }

    /**
     * Generate unique loan number
     */
    public static function generateLoanNumber(): string
    {
        return \DB::transaction(function () {
            $prefix = 'LOAN';
            $year = date('Y');
            $month = date('m');

            $lastLoan = self::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $sequence = $lastLoan ? (intval(substr($lastLoan->loan_number, -4)) + 1) : 1;

            return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $sequence);
        });
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the document
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the employee (borrower)
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get who disbursed the loan
     */
    public function disbursedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disbursed_by');
    }

    /**
     * Get creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get repayments
     */
    public function repayments(): HasMany
    {
        return $this->hasMany(LoanRepayment::class)->orderBy('installment_number');
    }

    // ==================== SCOPES ====================

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePendingDisbursement($query)
    {
        return $query->where('status', 'pending_disbursement');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['disbursed', 'repaying']);
    }

    public function scopeForEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    // ==================== ACTIONS ====================

    /**
     * Disburse the loan
     */
    public function disburse(int $disbursedBy, ?string $proofPath = null, ?string $notes = null): void
    {
        $this->status = 'disbursed';
        $this->disbursement_date = now()->toDateString();
        $this->disbursed_by = $disbursedBy;

        if ($proofPath) {
            $this->disbursement_proof = $proofPath;
        }

        if ($notes) {
            $this->notes = $notes;
        }

        $this->save();
    }

    /**
     * Record a repayment
     */
    public function recordRepayment(float $amount, int $recordedBy, array $data = []): LoanRepayment
    {
        $installmentNumber = $this->repayments()->count() + 1;

        $repayment = $this->repayments()->create([
            'installment_number' => $installmentNumber,
            'amount' => $amount,
            'payment_date' => $data['payment_date'] ?? now()->toDateString(),
            'payment_method' => $data['payment_method'] ?? null,
            'proof_file' => $data['proof_file'] ?? null,
            'notes' => $data['notes'] ?? null,
            'recorded_by' => $recordedBy,
        ]);

        // Recalculate totals from DB for accuracy
        // This prevents floating point drift and state mismatch
        $totalPaid = $this->repayments()->sum('amount');

        $this->total_paid = $totalPaid;
        $this->outstanding_amount = max(0, $this->loan_amount - $totalPaid);

        // Update status logic
        if ($this->outstanding_amount <= 0) {
            $this->status = 'paid_off';
        } else {
            // If debt remains, status is ALWAYS repaying (unless cancelled/etc, but here we are paying)
            $this->status = 'repaying';
        }

        $this->save();

        return $repayment;
    }

    /**
     * Check if loan is paid off
     */
    public function isPaidOff(): bool
    {
        return $this->status === 'paid_off' || $this->outstanding_amount <= 0;
    }

    /**
     * Get next installment number
     */
    public function getNextInstallmentNumber(): int
    {
        return $this->repayments()->count() + 1;
    }

    /**
     * Get repayment progress percentage
     */
    public function getProgressPercentage(): float
    {
        if ($this->loan_amount <= 0) {
            return 0;
        }
        return min(100, ((float) $this->total_paid / (float) $this->loan_amount) * 100);
    }

    /**
     * Get installment amount for a specific installment number
     * Returns last_installment for the final month, otherwise monthly_installment
     */
    public function getInstallmentAmount(int $installmentNumber): float
    {
        if ($installmentNumber >= $this->installment_months) {
            return (float) ($this->last_installment ?? $this->monthly_installment);
        }
        return (float) $this->monthly_installment;
    }

    /**
     * Get formatted last installment
     */
    public function getFormattedLastInstallmentAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->last_installment, 0, ',', '.');
    }

    // ==================== ACCESSORS ====================

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending_disbursement' => 'Menunggu Pencairan',
            'disbursed' => 'Dicairkan',
            'repaying' => 'Dalam Cicilan',
            'paid_off' => 'Lunas',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending_disbursement' => 'yellow',
            'disbursed' => 'blue',
            'repaying' => 'indigo',
            'paid_off' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get formatted loan amount
     */
    public function getFormattedLoanAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->loan_amount, 0, ',', '.');
    }

    /**
     * Get formatted monthly installment
     */
    public function getFormattedMonthlyInstallmentAttribute(): string
    {
        return 'Rp ' . number_format($this->monthly_installment, 0, ',', '.');
    }

    /**
     * Get formatted total paid
     */
    public function getFormattedTotalPaidAttribute(): string
    {
        return 'Rp ' . number_format($this->total_paid, 0, ',', '.');
    }

    /**
     * Get formatted outstanding amount
     */
    public function getFormattedOutstandingAttribute(): string
    {
        return 'Rp ' . number_format($this->outstanding_amount, 0, ',', '.');
    }
}
