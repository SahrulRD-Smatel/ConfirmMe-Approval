<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the auditable model
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Create an audit log
     */
    public static function log(string $event, $model = null, array $oldValues = [], array $newValues = []): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'event' => $event,
            'auditable_type' => $model ? get_class($model) : null,
            'auditable_id' => $model ? $model->id : null,
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ]);
    }

    /**
     * Get event label
     */
    public function getEventLabelAttribute(): string
    {
        return match ($this->event) {
            'login' => 'Login',
            'logout' => 'Logout',
            'login_failed' => 'Login Gagal',
            'password_reset' => 'Reset Password',
            'created' => 'Data Dibuat',
            'updated' => 'Data Diubah',
            'deleted' => 'Data Dihapus',
            'restored' => 'Data Dipulihkan',
            'exported' => 'Data Diekspor',
            'imported' => 'Data Diimpor',
            'attendance_imported' => 'Import Absensi',
            default => ucfirst(str_replace('_', ' ', $this->event)),
        };
    }

    /**
     * Get event icon
     */
    public function getEventIconAttribute(): string
    {
        return match ($this->event) {
            'login' => 'arrow-right-on-rectangle',
            'logout' => 'arrow-left-on-rectangle',
            'login_failed' => 'exclamation-triangle',
            'password_reset' => 'key',
            'created' => 'plus-circle',
            'updated' => 'pencil-square',
            'deleted' => 'trash',
            'restored' => 'arrow-path',
            'exported' => 'arrow-down-tray',
            'imported' => 'arrow-up-tray',
            'attendance_imported' => 'arrow-up-tray',
            default => 'information-circle',
        };
    }

    /**
     * Get event color
     */
    public function getEventColorAttribute(): string
    {
        return match ($this->event) {
            'login' => 'green',
            'logout' => 'gray',
            'login_failed' => 'red',
            'password_reset' => 'yellow',
            'created' => 'blue',
            'updated' => 'yellow',
            'deleted' => 'red',
            'restored' => 'green',
            'attendance_imported' => 'blue',
            default => 'gray',
        };
    }

    /**
     * Scope by event type
     */
    public function scopeEvent($query, string $event)
    {
        return $query->where('event', $event);
    }

    /**
     * Scope by user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope by model
     */
    public function scopeForModel($query, string $type, int $id)
    {
        return $query->where('auditable_type', $type)
            ->where('auditable_id', $id);
    }

    /**
     * Scope for recent logs
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
