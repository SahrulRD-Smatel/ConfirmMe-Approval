<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_id',
        'department',
        'position',
        'phone',
        'avatar',
        'is_active',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'division',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get documents created by this user (as requester)
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get documents where this user is the current approver
     */
    public function pendingApprovals(): HasMany
    {
        return $this->hasMany(Document::class, 'current_approver_id');
    }

    /**
     * Get all approval actions by this user
     */
    public function approvalActions(): HasMany
    {
        return $this->hasMany(DocumentApproval::class, 'approver_id');
    }

    /**
     * Get comments made by this user
     */
    public function comments(): HasMany
    {
        return $this->hasMany(DocumentComment::class);
    }

    /**
     * Get leave requests needed by this user
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /**
     * Get audit logs for this user
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get user's avatar URL or default
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=1E40AF&color=fff';
    }

    /**
     * Get user's initials
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return $initials;
    }

    /**
     * Deduct annual leave balance
     */
    public function deductLeaveBalance(int $days): void
    {
        $this->decrement('annual_leave_balance', $days);
    }

    /**
     * Increment sick leave counter
     */
    public function incrementSickLeave(int $days): void
    {
        $this->increment('sick_leave_used', $days);
    }

    /**
     * Get remaining annual leave (raw from database)
     */
    public function getRemainingLeaveAttribute(): int
    {
        return $this->annual_leave_balance ?? 12;
    }

    /**
     * Get effective annual leave balance (accounting for pending annual leaves)
     */
    public function getEffectiveAnnualLeaveBalanceAttribute(): int
    {
        $rawBalance = $this->annual_leave_balance ?? 12;
        
        $pendingAnnualLeaveDays = $this->leaveRequests()
            ->pending()
            ->where('type', 'annual')
            ->get()
            ->sum('duration');
            
        return max(0, $rawBalance - $pendingAnnualLeaveDays);
    }

    /**
     * Get all attendance records for this user.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
