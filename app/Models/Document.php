<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document_number',
        'title',
        'description',
        'approval_type',
        'po_category',
        'status',
        'estimated_budget',
        'approved_budget',
        'priority',
        'user_id',
        'current_approver_id',
        'department',
        'template',
        'metadata',
        'submitted_at',
        'approved_at',
        'rejected_at',
        'completed_at',
        'rejection_reason',
        'approval_hash',
        // Revision fields
        'revision_notes',
        'revision_count',
        'original_budget',
        'revision_requested_by',
        'revision_requested_at',
        // Requester confirmation fields
        'confirmed_amount',
        'amount_changed_by',
        'amount_changed_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'current_approver_id' => 'integer',
        'metadata' => 'array',
        'estimated_budget' => 'decimal:2',
        'approved_budget' => 'decimal:2',
        'original_budget' => 'decimal:2',
        'confirmed_amount' => 'decimal:2',
        'revision_count' => 'integer',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'completed_at' => 'datetime',
        'revision_requested_at' => 'datetime',
        'amount_changed_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            if (empty($document->document_number)) {
                $document->document_number = self::generateDocumentNumber();
            }
        });
    }

    /**
     * Generate unique document number
     */
    public static function generateDocumentNumber(): string
    {
        return \DB::transaction(function () {
            $prefix = 'DOC';
            $year = date('Y');
            $month = date('m');

            $lastDoc = self::withTrashed()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $sequence = $lastDoc ? (intval(substr($lastDoc->document_number, -4)) + 1) : 1;

            return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $sequence);
        });
    }

    /**
     * Generate approval hash for QR code
     */
    public function generateApprovalHash(): string
    {
        $data = [
            'id' => $this->id,
            'number' => $this->document_number,
            'status' => $this->status,
            'approved_at' => $this->approved_at?->toISOString(),
        ];

        $this->approval_hash = hash('sha256', json_encode($data) . config('app.key'));
        $this->save();

        return $this->approval_hash;
    }

    /**
     * Verify document using hash
     */
    public static function verifyHash(string $hash): ?self
    {
        return self::where('approval_hash', $hash)->first();
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the requester (creator) of the document
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for user() - the requester
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the current approver
     */
    public function currentApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_approver_id');
    }

    /**
     * Get all approval steps
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(DocumentApproval::class)->orderBy('approval_order');
    }

    /**
     * Get all attachments
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(DocumentAttachment::class);
    }

    /**
     * Get all comments
     */
    public function comments(): HasMany
    {
        return $this->hasMany(DocumentComment::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the purchase order for this document
     */
    public function purchaseOrder(): HasOne
    {
        return $this->hasOne(PurchaseOrder::class);
    }

    /**
     * Get the invoice for this document
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Get approval logs
     */
    public function approvalLogs(): HasMany
    {
        return $this->hasMany(ApprovalLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the employee loan for this document (for approval_type = 'loan')
     */
    public function employeeLoan(): HasOne
    {
        return $this->hasOne(EmployeeLoan::class);
    }

    // ==================== SCOPES ====================

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [
            'pending', 'supervisor_review', 'manager_review', 'hrd_review',
            'hrd_coordinator_review', 'purchasing_review',
            'director_review', 'finance_review', 'presiden_review',
        ]);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['completed', 'paid']);
    }

    public function scopeForApprover($query, User $user)
    {
        return $query->where('current_approver_id', $user->id);
    }

    public function scopeByDepartment($query, string $department)
    {
        return $query->where('department', $department);
    }

    public function scopeByApprovalType($query, string $type)
    {
        return $query->where('approval_type', $type);
    }

    // ==================== HELPERS ====================

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'pending' => 'Menunggu Review',
            'revision' => 'Perlu Direvisi',
            'pending_requester_confirmation' => 'Konfirmasi Nominal',
            'supervisor_review' => 'Review Supervisor',
            'manager_review' => 'Review Manager',
            'hrd_review' => 'Review HRD',
            'hrd_coordinator_review' => 'Review HRD Coordinator',
            'purchasing_review' => 'Review Purchasing',
            'director_review' => 'Review Direktur',
            'finance_review' => 'Review Finance',
            'presiden_review' => 'Review Presiden Direktur',
            'waiting_disbursement' => 'Menunggu Pencairan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'completed' => 'Selesai',
            'paid' => 'Dibayar',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'revision' => 'orange',
            'pending_requester_confirmation' => 'amber',
            'pending', 'supervisor_review', 'manager_review', 'hrd_review',
            'hrd_coordinator_review', 'purchasing_review',
            'director_review', 'finance_review', 'presiden_review' => 'yellow',
            'waiting_disbursement' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            'completed' => 'emerald',
            'paid' => 'purple',
            default => 'gray',
        };
    }

    /**
     * Get approval type label
     */
    public function getApprovalTypeLabelAttribute(): string
    {
        return match ($this->approval_type) {
            'only' => 'Approval Only',
            'po' => $this->po_category === 'internal' ? 'PO Internal' : 'Approval to PO',
            'invoice' => 'Approval to Invoice',
            'loan' => 'Pinjaman Karyawan',
            default => ucfirst($this->approval_type),
        };
    }

    /**
     * Get priority label
     */
    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'Rendah',
            'normal' => 'Normal',
            'high' => 'Tinggi',
            'urgent' => 'Urgent',
            default => ucfirst($this->priority),
        };
    }

    /**
     * Get priority color
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'gray',
            'normal' => 'blue',
            'high' => 'orange',
            'urgent' => 'red',
            default => 'gray',
        };
    }

    /**
     * Check if document can be edited
     */
    public function canEdit(): bool
    {
        return in_array($this->status, ['draft', 'revision', 'pending_requester_confirmation']);
    }

    /**
     * Check if document can be submitted
     */
    public function canSubmit(): bool
    {
        return in_array($this->status, ['draft', 'revision', 'pending_requester_confirmation']);
    }

    /**
     * Check if document is in approval process
     */
    public function isInApproval(): bool
    {
        return in_array($this->status, [
            'pending', 'supervisor_review', 'manager_review', 'hrd_review',
            'hrd_coordinator_review', 'purchasing_review',
            'director_review', 'finance_review', 'presiden_review',
        ]);
    }

    /**
     * Check if document is finalized (no more changes)
     */
    public function isFinalized(): bool
    {
        return in_array($this->status, ['approved', 'rejected', 'completed', 'paid', 'waiting_disbursement']);
    }

    /**
     * Get the next approval step
     */
    public function getNextApprovalStep(): ?string
    {
        // Note: Actual flow is determined by ApprovalWorkflowService based on approval_type
        return match ($this->status) {
            'pending' => 'supervisor_review',
            'supervisor_review' => 'manager_review',
            'manager_review' => 'hrd_review',
            'hrd_review' => 'finance_review',
            'finance_review' => 'director_review',
            'director_review' => 'presiden_review',
            'presiden_review' => 'approved',
            default => null,
        };
    }

    /**
     * Get current step role
     */
    public function getCurrentStepRole(): ?string
    {
        return match ($this->status) {
            'pending', 'supervisor_review' => 'supervisor',
            'manager_review' => 'manager',
            'hrd_review' => 'hrd',
            'hrd_coordinator_review' => 'hrd_coordinator',
            'purchasing_review' => 'purchasing',
            'director_review' => 'direktur',
            'finance_review' => 'finance',
            'presiden_review' => 'presiden_direktur',
            default => null,
        };
    }

    /**
     * Check if document is in revision
     */
    public function isRevision(): bool
    {
        return $this->status === 'revision';
    }

    /**
     * Get the user who requested revision
     */
    public function revisionRequester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revision_requested_by');
    }
}
