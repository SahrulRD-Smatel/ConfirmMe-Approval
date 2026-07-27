<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'type', // info, warning, urgent, danger
        // 'attachment', // Removed in favor of attachments table
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope a query to only include active announcements.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the user who created the announcement.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the attachments for the announcement.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(AnnouncementAttachment::class);
    }

    /**
     * Get CSS classes based on type
     */
    public function getTypeColorAttribute()
    {
        return match ($this->type) {
            'warning' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
            'urgent' => 'bg-orange-50 text-orange-800 border-orange-200',
            'danger' => 'bg-red-50 text-red-800 border-red-200',
            default => 'bg-blue-50 text-blue-800 border-blue-200', // info
        };
    }
}
