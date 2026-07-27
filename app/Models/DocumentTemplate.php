<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'approval_type',
        'fields',
        'workflow',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'fields' => 'array',
        'workflow' => 'array',
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
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by approval type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('approval_type', $type);
    }

    /**
     * Get approval type label
     */
    public function getApprovalTypeLabelAttribute(): string
    {
        return match ($this->approval_type) {
            'only' => 'Approval Only',
            'po' => 'Approval to PO',
            'invoice' => 'Approval to Invoice',
            'loan' => 'Employee Loan',
            default => ucfirst($this->approval_type),
        };
    }
}
