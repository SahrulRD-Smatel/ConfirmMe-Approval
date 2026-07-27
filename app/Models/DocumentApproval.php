<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'approver_id',
        'approver_role',
        'approval_order',
        'status',
        'comment',
        'approved_amount',
        'action_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'approval_order' => 'integer',
        'approved_amount' => 'decimal:2',
        'action_at' => 'datetime',
    ];

    /**
     * Get the document
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the approver
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Scope for pending approvals
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'skipped' => 'Dilewati',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'skipped' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get role label
     */
    public function getRoleLabelAttribute(): string
    {
        // Use config role_labels for proper display names
        $roleLabels = config('workflow.role_labels', []);

        return $roleLabels[$this->approver_role] ?? ucfirst(str_replace('_', ' ', $this->approver_role));
    }

    /**
     * Approve this step
     */
    public function approve(?string $comment = null, ?float $approvedAmount = null): void
    {
        $this->update([
            'status' => 'approved',
            'comment' => $comment,
            'approved_amount' => $approvedAmount,
            'action_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Reject this step
     */
    public function reject(string $comment): void
    {
        $this->update([
            'status' => 'rejected',
            'comment' => $comment,
            'action_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
