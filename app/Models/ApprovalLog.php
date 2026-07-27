<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'action',
        'from_status',
        'to_status',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the document
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a log entry
     */
    public static function log(Document $document, string $action, ?string $description = null, array $metadata = []): self
    {
        return self::create([
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'from_status' => $document->getOriginal('status'),
            'to_status' => $document->status,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get action label
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'created' => 'Dibuat',
            'submitted' => 'Dikirim untuk Approval',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'commented' => 'Komentar Ditambahkan',
            'updated' => 'Diperbarui',
            'completed' => 'Diselesaikan',
            'paid' => 'Dibayar',
            'po_generated' => 'PO Dibuat',
            'invoice_generated' => 'Invoice Dibuat',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }

    /**
     * Get action icon
     */
    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'created' => 'plus-circle',
            'submitted' => 'paper-airplane',
            'approved' => 'check-circle',
            'rejected' => 'x-circle',
            'commented' => 'chat-bubble-left',
            'updated' => 'pencil',
            'completed' => 'flag',
            'paid' => 'banknotes',
            'po_generated' => 'document-text',
            'invoice_generated' => 'receipt-percent',
            default => 'information-circle',
        };
    }

    /**
     * Get action color
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'created' => 'blue',
            'submitted' => 'indigo',
            'approved' => 'green',
            'rejected' => 'red',
            'commented' => 'gray',
            'updated' => 'yellow',
            'completed' => 'emerald',
            'paid' => 'purple',
            default => 'gray',
        };
    }
}
