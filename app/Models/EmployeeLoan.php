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

        // Synchronize document status
        if ($this->document) {
            $this->document->update(['status' => 'disbursed']);
        }
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

        // Synchronize document status
        if ($this->document) {
            $targetDocStatus = $this->isPaidOff() ? 'paid' : 'repaying';
            $this->document->update(['status' => $targetDocStatus]);
        }

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
        return 'Rp ' . number_format($this->loan_amount > 0 ? $this->outstanding_amount : 0, 0, ',', '.');
    }

    /**
     * Synchronize and recalculate loan details from associated document
     * Useful for recovering loan data created from dynamic form templates
     */
    public function syncFromDocument(): bool
    {
        $document = $this->document ?: ($this->document_id ? Document::find($this->document_id) : null);
        if (!$document) {
            return false;
        }

        $metadata = is_array($document->metadata) ? $document->metadata : [];

        // 1. Resolve Loan Amount
        $loanAmount = (float) ($document->approved_budget ?? 0);
        if ($loanAmount <= 0) {
            $loanAmount = (float) ($document->estimated_budget ?? 0);
        }

        if ($loanAmount <= 0 && !empty($metadata)) {
            // First pass: specific loan amount keys
            foreach ($metadata as $key => $val) {
                if (is_scalar($val) && !is_array($val)) {
                    $keyLow = strtolower(trim((string)$key));
                    if (preg_match('/loan_amount|jumlah_pengajuan_pinjaman|jumlah_pinjaman|nominal_pinjaman|besar_pinjaman|nilai_pinjaman|pengajuan_pinjaman|^pinjaman$/i', $keyLow)) {
                        $parsed = (float) preg_replace('/[^\d]/', '', (string)$val);
                        if ($parsed > 0) {
                            $loanAmount = $parsed;
                            break;
                        }
                    }
                }
            }

            // Second pass: any monetary/nominal key
            if ($loanAmount <= 0) {
                foreach ($metadata as $key => $val) {
                    if (is_scalar($val) && !is_array($val)) {
                        $keyLow = strtolower(trim((string)$key));
                        if (!preg_match('/tenor|bulan|jangka|lama|cicilan|hari|jam|qty|kuantitas/i', $keyLow) &&
                            preg_match('/pinjaman|nominal|jumlah|harga|biaya/i', $keyLow)) {
                            $parsed = (float) preg_replace('/[^\d]/', '', (string)$val);
                            if ($parsed > 0) {
                                $loanAmount = $parsed;
                                break;
                            }
                        }
                    }
                }
            }
        }

        // 2. Resolve Tenor / Installment Months
        $months = 0;
        if (!empty($metadata['installment_months'])) {
            $months = (int) $metadata['installment_months'];
        } elseif (!empty($metadata['installment_count'])) {
            $months = (int) $metadata['installment_count'];
        } else {
            foreach ($metadata as $key => $val) {
                if (is_scalar($val)) {
                    $keyLow = strtolower(trim((string)$key));
                    if (preg_match('/jangka_waktu|tenor|lama_pinjaman|durasi|cicilan|bulan|installment/i', $keyLow)) {
                        if (preg_match('/\d+/', (string)$val, $matches)) {
                            $months = (int) $matches[0];
                            break;
                        }
                    }
                }
            }
        }

        if ($months < 1) {
            $months = 12;
        }

        // 3. Resolve Bank Information
        $bankName = $this->bank_name ?: ($metadata['bank_name'] ?? null);
        $bankNumber = $this->bank_account_number ?: ($metadata['bank_account_number'] ?? $metadata['nomor_rekening'] ?? null);
        $bankAccountName = $this->bank_account_name ?: ($metadata['bank_account_name'] ?? $metadata['nama_rekening'] ?? null);

        if (!$bankName || !$bankNumber || !$bankAccountName) {
            foreach ($metadata as $k => $v) {
                if (is_scalar($v) && !empty($v)) {
                    $kLow = strtolower((string)$k);
                    if (!$bankName && preg_match('/bank_name|nama_bank|^bank$/i', $kLow)) {
                        $bankName = (string)$v;
                    } elseif (!$bankNumber && preg_match('/bank_account_number|nomor_rekening|no_rek|rekening/i', $kLow)) {
                        $bankNumber = (string)$v;
                    } elseif (!$bankAccountName && preg_match('/bank_account_name|nama_rekening|atas_nama/i', $kLow)) {
                        $bankAccountName = (string)$v;
                    }
                }
            }
        }

        $bankName = $bankName ?: $document->user?->bank_name;
        $bankNumber = $bankNumber ?: $document->user?->bank_account_number;
        $bankAccountName = $bankAccountName ?: ($document->user?->bank_account_name ?: $document->user?->name);

        if ($loanAmount > 0) {
            $rawMonthly = $loanAmount / $months;
            $monthlyInstallment = ceil($rawMonthly / 1000) * 1000;
            $lastInstallment = $loanAmount - ($monthlyInstallment * ($months - 1));

            if ($lastInstallment <= 0) {
                $monthlyInstallment = floor($loanAmount / $months / 1000) * 1000;
                $lastInstallment = $loanAmount - ($monthlyInstallment * ($months - 1));
            }

            $totalPaid = (float) $this->total_paid;
            $outstanding = max(0, $loanAmount - $totalPaid);

            $this->update([
                'loan_amount' => $loanAmount,
                'installment_months' => $months,
                'monthly_installment' => $monthlyInstallment,
                'last_installment' => $lastInstallment,
                'outstanding_amount' => $outstanding,
                'bank_name' => $bankName,
                'bank_account_number' => $bankNumber,
                'bank_account_name' => $bankAccountName,
            ]);

            // Synchronize document budgets if they were empty
            $docUpdates = [];
            if ((float)($document->estimated_budget ?? 0) <= 0) {
                $docUpdates['estimated_budget'] = $loanAmount;
            }
            if ((float)($document->approved_budget ?? 0) <= 0) {
                $docUpdates['approved_budget'] = $loanAmount;
            }
            if ($this->isPaidOff() && $document->status !== 'paid') {
                $docUpdates['status'] = 'paid';
            } elseif ($this->status === 'disbursed' && $document->status === 'waiting_disbursement') {
                $docUpdates['status'] = 'disbursed';
            } elseif ($this->status === 'repaying' && in_array($document->status, ['waiting_disbursement', 'disbursed'])) {
                $docUpdates['status'] = 'repaying';
            }
            if (!empty($docUpdates)) {
                $document->update($docUpdates);
            }

            return true;
        }

        // Even if loanAmount was already set, ensure document status is synchronized
        if ($this->isPaidOff() && $document->status !== 'paid') {
            $document->update(['status' => 'paid']);
        } elseif ($this->status === 'disbursed' && $document->status === 'waiting_disbursement') {
            $document->update(['status' => 'disbursed']);
        } elseif ($this->status === 'repaying' && in_array($document->status, ['waiting_disbursement', 'disbursed'])) {
            $document->update(['status' => 'repaying']);
        }

        return false;
    }
}
