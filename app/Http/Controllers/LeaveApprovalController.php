<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Services\LeaveNotificationService;
use Illuminate\Http\Request;

class LeaveApprovalController extends Controller
{
    protected LeaveNotificationService $notificationService;

    public function __construct(LeaveNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Check if a department belongs to HR.
     */
    private function isHRDepartment(?string $department): bool
    {
        return $department === 'Human Resources';
    }

    /**
     * Check if the current user is authorized to act on a specific leave request.
     * Now uses the 4-step system with current_approver_id.
     */
    private function isAuthorizedFor(LeaveRequest $leaveRequest): bool
    {
        $user = auth()->user();

        // Self-approval is never allowed
        if ((int) $leaveRequest->user_id === (int) $user->id) {
            return false;
        }

        // If current_approver_id is set, only that person can act
        if ($leaveRequest->current_approver_id) {
            return (int) $leaveRequest->current_approver_id === (int) $user->id;
        }

        // Fallback: role-based authorization for backward compatibility
        return $this->isAuthorizedByRole($leaveRequest, $user);
    }

    /**
     * Role-based authorization fallback (backward compat with old 'pending' and 'pending_hr' statuses).
     */
    private function isAuthorizedByRole(LeaveRequest $leaveRequest, User $user): bool
    {
        $requester = $leaveRequest->user;

        // Step 1: pending_supervisor — Supervisor in same dept
        if ($leaveRequest->status === 'pending_supervisor') {
            if ($user->hasRole('supervisor') && $user->department === $requester->department) {
                return true;
            }
            // Manager can also approve if no supervisor available
            if ($user->hasRole('manager') && $user->department === $requester->department) {
                return true;
            }
        }

        // Step 2: pending_manager — Manager in same dept or division
        if ($leaveRequest->status === 'pending_manager') {
            if ($user->hasRole('manager') && $user->department === $requester->department) {
                return true;
            }
            // Division director can approve if no manager
            if ($user->hasAnyRole(['direktur_hr', 'direktur_finance', 'direktur_it', 'direktur_sales', 'direktur_operasional'])) {
                return true;
            }
        }

        // Step 3: pending_hrd — HRD Coordinator or HR Manager
        if ($leaveRequest->status === 'pending_hrd') {
            if ($user->hasRole('hrd_coordinator')) {
                return true;
            }
            if ($user->hasRole('hrd')) {
                return true;
            }
            if ($user->hasRole('manager') && $this->isHRDepartment($user->department)) {
                return true;
            }
        }

        // Step 4: pending_hr_final — HR Manager final validation
        if (in_array($leaveRequest->status, ['pending_hr_final', 'pending_hr'])) {
            if ($user->hasRole('manager') && $this->isHRDepartment($user->department)) {
                return true;
            }
            if ($user->hasRole('hrd')) {
                return true;
            }
            if ($user->hasRole('admin')) {
                return true;
            }
        }

        // Legacy: 'pending' status (before migration runs)
        if ($leaveRequest->status === 'pending') {
            return $this->isAuthorizedLegacy($leaveRequest, $user);
        }

        return false;
    }

    /**
     * Legacy authorization for old 'pending' status (backward compat).
     */
    private function isAuthorizedLegacy(LeaveRequest $leaveRequest, User $user): bool
    {
        $requester = $leaveRequest->user;

        if ($user->hasRole('supervisor') && $requester->hasRole('staff') && $requester->department === $user->department) {
            return true;
        }
        if ($user->hasRole('manager') && !$requester->hasAnyRole(['manager', 'direktur_hr', 'direktur_finance', 'direktur_it', 'direktur_sales', 'direktur_operasional', 'presiden_direktur'])) {
            if ($requester->department === $user->department || $this->isHRDepartment($user->department)) {
                return true;
            }
        }
        if ($user->hasAnyRole(['direktur_finance', 'direktur_it', 'direktur_sales', 'direktur_operasional'])) {
            return $requester->hasRole('manager');
        }
        if ($user->hasRole('direktur_hr')) {
            return $requester->hasRole('manager') && $this->isHRDepartment($requester->department);
        }
        if ($user->hasRole('presiden_direktur')) {
            return $requester->hasAnyRole(['direktur_hr', 'direktur_finance', 'direktur_it', 'direktur_sales', 'direktur_operasional']);
        }

        return false;
    }

    /**
     * Find the approver for a given step.
     */
    private function findApproverForStep(LeaveRequest $leaveRequest, int $step): ?User
    {
        $requester = $leaveRequest->user;
        $requesterId = $requester->id;

        return match ($step) {
            // Step 1: Supervisor in same department
            1 => User::role('supervisor')
                ->where('department', $requester->department)
                ->where('is_active', true)
                ->where('id', '!=', $requesterId)
                ->first()
                ?? User::role('manager')
                    ->where('department', $requester->department)
                    ->where('is_active', true)
                    ->where('id', '!=', $requesterId)
                    ->first(),

            // Step 2: Manager in same department
            2 => User::role('manager')
                ->where('department', $requester->department)
                ->where('is_active', true)
                ->where('id', '!=', $requesterId)
                ->first(),

            // Step 3: HRD Coordinator or HRD
            3 => User::role('hrd_coordinator')
                ->where('is_active', true)
                ->where('id', '!=', $requesterId)
                ->first()
                ?? User::role('hrd')
                    ->where('is_active', true)
                    ->where('id', '!=', $requesterId)
                    ->first(),

            // Step 4: HR Manager (manager in HR dept) or admin
            4 => User::role('manager')
                ->where('department', 'Human Resources')
                ->where('is_active', true)
                ->where('id', '!=', $requesterId)
                ->first()
                ?? User::role('hrd')
                    ->where('is_active', true)
                    ->where('id', '!=', $requesterId)
                    ->first()
                ?? User::role('admin')
                    ->where('is_active', true)
                    ->where('id', '!=', $requesterId)
                    ->first(),

            default => null,
        };
    }

    /**
     * Initialize the 4-step approval workflow for a leave request.
     * Called when the leave request is first submitted.
     */
    public function initializeLeaveWorkflow(LeaveRequest $leaveRequest): void
    {
        $startStep = $leaveRequest->determineStartingStep();
        $approver = $this->findApproverForStep($leaveRequest, $startStep);

        // If no approver found for this step, try the next step
        while (!$approver && $startStep < 4) {
            $startStep++;
            $approver = $this->findApproverForStep($leaveRequest, $startStep);
        }

        $status = LeaveRequest::statusForStep($startStep);

        $leaveRequest->update([
            'status' => $status,
            'current_step' => $startStep,
            'current_approver_id' => $approver?->id,
        ]);
    }

    /**
     * Display a listing of pending leave requests for approval.
     */
    public function index()
    {
        $user = auth()->user();

        $query = LeaveRequest::with(['user', 'currentApprover']);

        // Primary: Show requests assigned to me
        $query->where(function ($q) use ($user) {
            // Requests specifically assigned to this approver
            $q->where('current_approver_id', $user->id);

            // Also show requests that match role-based authorization (fallback for unassigned)
            $q->orWhere(function ($sub) use ($user) {
                $sub->whereNull('current_approver_id');
                $this->applyRoleBasedFilter($sub, $user);
            });
        });

        // Exclude own requests
        $query->where('user_id', '!=', $user->id);

        // Only show in-progress requests
        $query->whereIn('status', [
            'pending', 'pending_supervisor', 'pending_manager',
            'pending_hrd', 'pending_hr', 'pending_hr_final',
        ]);

        $pendingRequests = $query->latest()->paginate(15);

        // Get recent history — only visible to HR department & admin
        $recentlyProcessed = collect();
        if ($this->isHRDepartment($user->department) || $user->hasRole('admin')) {
            $recentlyProcessed = LeaveRequest::with('user', 'approver')
                ->whereIn('status', ['approved', 'rejected'])
                ->whereNotNull('approved_at')
                ->where('approved_at', '>=', now()->subDays(7))
                ->latest('approved_at')
                ->take(10)
                ->get();
        }

        return view('leave-approvals.index', compact('pendingRequests', 'recentlyProcessed'));
    }

    /**
     * Apply role-based filter for the leave approval index query.
     * Used as fallback when current_approver_id is not set.
     */
    private function applyRoleBasedFilter($query, User $user): void
    {
        if ($user->hasRole('supervisor')) {
            $query->where(function ($q) use ($user) {
                $q->whereIn('status', ['pending', 'pending_supervisor'])
                    ->whereHas('user', fn($u) => $u->where('department', $user->department)->role('staff'));
            });
        } elseif ($user->hasRole('manager') && !$this->isHRDepartment($user->department)) {
            $query->whereIn('status', ['pending', 'pending_supervisor', 'pending_manager'])
                ->whereHas('user', fn($u) => $u->where('department', $user->department));
        } elseif ($user->hasRole('manager') && $this->isHRDepartment($user->department)) {
            $query->where(function ($q) use ($user) {
                $q->whereIn('status', ['pending_hr', 'pending_hr_final', 'pending_hrd'])
                    ->where('user_id', '!=', $user->id);
            });
        } elseif ($user->hasRole('hrd_coordinator')) {
            $query->whereIn('status', ['pending_hrd']);
        } elseif ($user->hasRole('hrd')) {
            $query->whereIn('status', ['pending_hrd', 'pending_hr', 'pending_hr_final']);
        } elseif ($user->hasAnyRole(['direktur_finance', 'direktur_it', 'direktur_sales', 'direktur_operasional'])) {
            $query->whereIn('status', ['pending', 'pending_manager'])
                ->whereHas('user', fn($u) => $u->role('manager'));
        } elseif ($user->hasRole('direktur_hr')) {
            $query->whereIn('status', ['pending', 'pending_manager'])
                ->whereHas('user', fn($u) => $u->role('manager')->where('department', 'Human Resources'));
        } elseif ($user->hasRole('presiden_direktur')) {
            $query->where('status', 'pending')
                ->whereHas('user', fn($u) => $u->whereHas('roles', fn($r) => $r->where('name', 'like', 'direktur%')));
        } elseif ($user->hasRole('admin')) {
            // Admin can see all pending
        } else {
            $query->where('id', 0); // Show nothing
        }
    }

    /**
     * Approve a leave request — moves to the next step in 4-step workflow.
     */
    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        $user = auth()->user();

        // Authorization check
        if (!$this->isAuthorizedFor($leaveRequest)) {
            abort(403, 'Anda tidak berhak menyetujui pengajuan cuti ini.');
        }

        $currentStep = $leaveRequest->current_step ?? 1;
        $nextStep = $currentStep + 1;

        // If we've passed step 4, approve fully
        if ($nextStep > 4) {
            return $this->finalizeApproval($leaveRequest, $user);
        }

        // Find next approver
        $nextApprover = $this->findApproverForStep($leaveRequest, $nextStep);

        // If no approver for next step, try subsequent steps
        while (!$nextApprover && $nextStep < 4) {
            $nextStep++;
            $nextApprover = $this->findApproverForStep($leaveRequest, $nextStep);
        }

        // If still no approver after step 4, finalize
        if (!$nextApprover && $nextStep >= 4) {
            return $this->finalizeApproval($leaveRequest, $user);
        }

        $nextStatus = LeaveRequest::statusForStep($nextStep);

        $leaveRequest->update([
            'status' => $nextStatus,
            'current_step' => $nextStep,
            'current_approver_id' => $nextApprover?->id,
        ]);

        // Send notification to next approver
        try {
            $this->notificationService->sendTier1ApprovedNotification($leaveRequest, $user);
        } catch (\Exception $e) {
            \Log::warning('Failed to send leave step notification: ' . $e->getMessage());
        }

        $stepLabel = match ($nextStep) {
            2 => 'Manager',
            3 => 'HRD Coordinator',
            4 => 'HR Manager',
            default => 'approver berikutnya',
        };

        return back()->with('success', "Cuti disetujui. Menunggu persetujuan {$stepLabel}.");
    }

    /**
     * Finalize leave approval — all steps complete.
     */
    private function finalizeApproval(LeaveRequest $leaveRequest, User $approver)
    {
        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'current_approver_id' => null,
        ]);

        // Deduct/increment leave balance
        $this->applyLeaveBalance($leaveRequest);

        // Send approved email to staff
        try {
            $this->notificationService->sendApprovedNotification($leaveRequest, $approver);
        } catch (\Exception $e) {
            \Log::warning('Failed to send leave approved notification: ' . $e->getMessage());
        }

        return back()->with('success', 'Cuti disetujui sepenuhnya.');
    }

    /**
     * Reject a leave request.
     */
    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        // Authorization check
        if (!$this->isAuthorizedFor($leaveRequest)) {
            abort(403, 'Anda tidak berhak menolak pengajuan cuti ini.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();

        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'] ?? null,
            'current_approver_id' => null,
        ]);

        // Send rejection email to staff
        try {
            $this->notificationService->sendRejectedNotification($leaveRequest, $user);
        } catch (\Exception $e) {
            \Log::warning('Failed to send leave rejection notification: ' . $e->getMessage());
        }

        return back()->with('success', 'Pengajuan cuti ditolak.');
    }

    /**
     * Apply leave balance changes (deduct annual or increment sick).
     */
    private function applyLeaveBalance(LeaveRequest $leaveRequest): void
    {
        if ($leaveRequest->type === 'annual') {
            $leaveRequest->user->deductLeaveBalance($leaveRequest->duration);
        }

        if ($leaveRequest->type === 'sick') {
            $leaveRequest->user->incrementSickLeave($leaveRequest->duration);
        }
    }
}
