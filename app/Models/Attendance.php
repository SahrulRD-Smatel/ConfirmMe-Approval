<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'type',
        'latitude',
        'longitude',
        'location_name',
        'office_location_id',
        'photo',
        'device_info',
        'status',
        'is_wfh',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_wfh' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function officeLocation()
    {
        return $this->belongsTo(OfficeLocation::class);
    }

    // ==================== SCOPES ====================

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeCheckIns($query)
    {
        return $query->where('type', 'check_in');
    }

    public function scopeCheckOuts($query)
    {
        return $query->where('type', 'check_out');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)
            ->whereYear('date', now()->year);
    }

    // ==================== HELPERS ====================

    /**
     * Get type label in Indonesian.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'check_in' => 'Check In',
            'check_out' => 'Check Out',
            'break_start' => 'Mulai Istirahat',
            'break_end' => 'Selesai Istirahat',
            default => ucfirst($this->type),
        };
    }

    /**
     * Get status label in Indonesian.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'on_time' => 'Tepat Waktu',
            'late' => 'Terlambat',
            'early_leave' => 'Pulang Lebih Awal',
            'wfh' => 'Work From Home',
            'invalid_location' => 'Lokasi Tidak Valid',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'on_time' => 'green',
            'late' => 'orange',
            'early_leave' => 'yellow',
            'wfh' => 'blue',
            'invalid_location' => 'red',
            default => 'gray',
        };
    }

    /**
     * Check if user has checked in today.
     */
    public static function hasCheckedInToday(int $userId): bool
    {
        return static::forUser($userId)->today()->checkIns()->exists();
    }

    /**
     * Check if user has checked out today.
     */
    public static function hasCheckedOutToday(int $userId): bool
    {
        return static::forUser($userId)->today()->checkOuts()->exists();
    }

    /**
     * Get today's check-in for a user.
     */
    public static function todayCheckIn(int $userId): ?self
    {
        return static::forUser($userId)->today()->checkIns()->first();
    }

    /**
     * Get today's check-out for a user.
     */
    public static function todayCheckOut(int $userId): ?self
    {
        return static::forUser($userId)->today()->checkOuts()->first();
    }

    /**
     * Calculate work duration in minutes (check-in to check-out).
     */
    public static function workDurationToday(int $userId): ?int
    {
        $checkIn = static::todayCheckIn($userId);
        $checkOut = static::todayCheckOut($userId);

        if (!$checkIn || !$checkOut) {
            return null;
        }

        return $checkIn->created_at->diffInMinutes($checkOut->created_at);
    }

    /**
     * Format work duration as H:i string.
     */
    public static function workDurationFormatted(int $userId): string
    {
        $minutes = static::workDurationToday($userId);

        if ($minutes === null) {
            return '-';
        }

        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        return sprintf('%d jam %d menit', $hours, $mins);
    }
}
