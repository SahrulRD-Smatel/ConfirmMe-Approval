<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'type', // annual, sick, unpaid, other
        'start_date',
        'end_date',
        'reason',
        'status', // pending, pending_supervisor, pending_manager, pending_hrd, pending_hr_final, approved, rejected
        'current_step',
        'current_approver_id',
        'approved_by',
        'approved_at',
        'attachment',
        'rejection_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'current_step' => 'integer',
    ];

    /**
     * Get the user who requested the leave.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the approver.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the current assigned approver.
     */
    public function currentApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_approver_id');
    }

    /**
     * Get duration in days
     */
    public function getDurationAttribute()
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Scope: Pending requests
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'pending_supervisor', 'pending_manager', 'pending_hrd', 'pending_hr', 'pending_hr_final']);
    }

    /**
     * Scope: Approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Active today (for Who's Off Today)
     */
    public function scopeActiveToday($query)
    {
        return $query->approved()
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now());
    }

    /**
     * Get type label in Indonesian
     */
    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'annual' => 'Cuti Tahunan',
            'sick' => 'Sakit',
            'unpaid' => 'Cuti Tanpa Gaji',
            'other' => 'Lainnya',
            default => $this->type,
        };
    }

    /**
     * Get status label in Indonesian
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Menunggu Proses',
            'pending_supervisor' => 'Menunggu Atasan',
            'pending_manager' => 'Menunggu Manager',
            'pending_hrd' => 'Menunggu HRD',
            'pending_hr' => 'Menunggu HR',
            'pending_hr_final' => 'Validasi HR Final',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => 'gray',
            'pending_supervisor' => 'yellow',
            'pending_manager' => 'yellow',
            'pending_hrd' => 'orange',
            'pending_hr' => 'orange',
            'pending_hr_final' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get approver role info based on current step
     */
    public function getApproverInfoAttribute(): string
    {
        // If we have a current approver assigned, show them
        if ($this->currentApprover) {
            return $this->currentApprover->name . ' (' . $this->currentApprover->position . ')';
        }

        return match ($this->status) {
            'pending_supervisor' => 'Atasan Langsung',
            'pending_manager' => 'Manager',
            'pending_hrd' => 'HRD Coordinator',
            'pending_hr', 'pending_hr_final' => 'HR Manager',
            'approved' => 'Selesai',
            'rejected' => 'Ditolak',
            default => 'Dalam Proses',
        };
    }

    /**
     * Determine the starting step for a leave request based on the requester's role.
     * Higher-level requesters skip lower approval steps automatically.
     *
     * Steps:
     *   1 = Atasan langsung (supervisor in same dept)
     *   2 = Manager
     *   3 = HRD Coordinator / Head GM
     *   4 = HR Manager / Final
     *
     * Auto-skip rules:
     *   Staff       → starts at step 1 (atasan)
     *   Supervisor  → starts at step 2 (manager)
     *   Manager     → starts at step 3 (HRD coordinator)
     *   Head/GM     → starts at step 4 (HR final)
     *   Direktur    → starts at step 4 (HR final / Presdir)
     */
    public function determineStartingStep(): int
    {
        $user = $this->user;

        if ($user->hasAnyRole(['direktur_hr', 'direktur_finance', 'direktur_it', 'direktur_sales', 'direktur_operasional'])) {
            return 4; // Direktur → langsung HR final / Presdir
        }

        if ($user->hasRole('manager')) {
            return 3; // Manager → skip atasan & manager step
        }

        if ($user->hasRole('supervisor')) {
            return 2; // Supervisor → skip step 1 (atasan)
        }

        return 1; // Staff → mulai dari step 1 (atasan langsung)
    }

    /**
     * Get the status name for a given step number.
     */
    public static function statusForStep(int $step): string
    {
        return match ($step) {
            1 => 'pending_supervisor',
            2 => 'pending_manager',
            3 => 'pending_hr',
            4 => 'pending_hr_final',
            default => 'approved',
        };
    }

    /**
     * Check if this leave request is still in approval process.
     */
    public function isInApproval(): bool
    {
        return in_array($this->status, [
            'pending', 'pending_supervisor', 'pending_manager',
            'pending_hrd', 'pending_hr', 'pending_hr_final',
        ]);
    }
}
