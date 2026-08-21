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

        // Global fallback: Admin or users with view-all-documents can approve
        if ($user->hasPermissionTo('view-all-documents')) {
            return true;
        }

        // Step 1 & 2: Same department approval
        if (in_array($leaveRequest->status, ['pending', 'pending_supervisor', 'pending_manager'])) {
            if ($user->hasPermissionTo('approve-document') && $user->department === $requester->department) {
                return true;
            }
        }

        // Step 3 & 4: HR Department approval
        if (in_array($leaveRequest->status, ['pending_hrd', 'pending_hr', 'pending_hr_final'])) {
            if ($user->hasPermissionTo('manage-attendance')) {
                return true;
            }
            if ($user->hasPermissionTo('approve-document') && $this->isHRDepartment($user->department)) {
                return true;
            }
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
            // Step 1: Supervisor/Manager in same department (has approve-document)
            1 => User::permission('approve-document')
                ->where('department', $requester->department)
                ->where('is_active', true)
                ->where('id', '!=', $requesterId)
                ->orderBy('id', 'asc')
                ->first(),

            // Step 2: Manager/Director in same department (has view-department-documents)
            2 => User::permission('view-department-documents')
                ->where('department', $requester->department)
                ->where('is_active', true)
                ->where('id', '!=', $requesterId)
                ->orderBy('id', 'asc')
                ->first(),

            // Step 3: HR Coordinator (has manage-attendance)
            3 => User::permission('manage-attendance')
                ->where('is_active', true)
                ->where('id', '!=', $requesterId)
                ->first(),

            // Step 4: HR Manager / Admin
            4 => User::permission('manage-attendance')
                ->where('department', 'Human Resources')
                ->where('is_active', true)
                ->where('id', '!=', $requesterId)
                ->first()
                ?? User::permission('view-all-documents')
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
        // Global viewers (Admin, etc)
        if ($user->hasPermissionTo('view-all-documents')) {
            // Can see all pending
            return;
        }

        // HR Department viewers
        if ($user->hasPermissionTo('manage-attendance')) {
            if ($user->department === 'Human Resources') {
                $query->whereIn('status', ['pending_hr', 'pending_hr_final', 'pending_hrd']);
            } else {
                $query->whereIn('status', ['pending_hrd']);
            }
            return;
        }

        // Department-level viewers
        if ($user->hasPermissionTo('view-department-documents')) {
            $query->whereIn('status', ['pending', 'pending_supervisor', 'pending_manager'])
                ->whereHas('user', fn($u) => $u->where('department', $user->department));
            return;
        }

        // Team-level viewers (Supervisor)
        if ($user->hasPermissionTo('approve-document')) {
            $query->whereIn('status', ['pending', 'pending_supervisor'])
                ->whereHas('user', fn($u) => $u->where('department', $user->department));
            return;
        }

        // Fallback: Show nothing if no permissions match
        $query->where('id', 0);
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
        
        // After Step 1 (Atasan/Supervisor), it should go straight to Step 3 (HRD), skipping Manager
        $nextStep = $currentStep === 1 ? 3 : $currentStep + 1;

        // If we've passed step 4, approve fully
        if ($nextStep > 4) {
            return $this->finalizeApproval($leaveRequest, $user);
        }

        // Find next approver
        $nextApprover = $this->findApproverForStep($leaveRequest, $nextStep);

        // Auto-skip steps if there is no approver OR if the next approver is the exact same user who just approved
        while (($nextApprover === null || $nextApprover->id === $user->id) && $nextStep <= 4) {
            if ($nextStep === 4) {
                $nextStep++;
                break;
            }
            $nextStep++;
            $nextApprover = $this->findApproverForStep($leaveRequest, $nextStep);
        }

        // If we've passed step 4, finalize
        if ($nextStep > 4) {
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

        \Illuminate\Support\Facades\Cache::forget("sidebar_badges_{$user->id}");

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

        \Illuminate\Support\Facades\Cache::forget("sidebar_badges_{$approver->id}");

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

        \Illuminate\Support\Facades\Cache::forget("sidebar_badges_{$user->id}");

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
