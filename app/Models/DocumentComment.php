<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document_id',
        'user_id',
        'comment',
        'is_internal',
        'parent_id',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    /**
     * Get the document
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the commenter
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get parent comment
     */
    public function parent()
    {
        return $this->belongsTo(DocumentComment::class, 'parent_id');
    }

    /**
     * Get replies
     */
    public function replies()
    {
        return $this->hasMany(DocumentComment::class, 'parent_id');
    }

    /**
     * Scope for public comments only
     */
    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }

    /**
     * Scope for internal comments only
     */
    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    /**
     * Scope for root comments (no parent)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
