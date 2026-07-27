<?php

namespace App\Services;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Mail\LeaveRequestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * Leave Email Notification Service
 *
 * Handles email notifications for leave request workflow:
 * - Staff submits → email to approver (supervisor/manager/director)
 * - Tier 1 approved (pending → pending_hr) → email to HR Manager + staff
 * - Tier 2 approved (pending_hr → approved) → email to staff
 * - Rejected → email to staff
 */
class LeaveNotificationService
{
    /**
     * Send notification when a leave request is submitted
     */
    public function sendSubmittedNotification(LeaveRequest $leaveRequest): void
    {
        try {
            $requester = $leaveRequest->user;
            $approver = $this->findApprover($leaveRequest);

            if (!$approver) {
                Log::warning("No approver found for leave request #{$leaveRequest->id} by {$requester->name}");
                return;
            }

            $mail = new LeaveRequestMail(
                leaveRequest: $leaveRequest,
                action: 'submitted',
                recipient: $approver,
                actor: $requester,
            );

            Mail::to($approver)->send($mail);

            Log::info("Sent leave submitted email for #{$leaveRequest->id} to {$approver->email}");
        } catch (\Throwable $e) {
            Log::error("Failed to send leave submitted email for #{$leaveRequest->id}: " . $e->getMessage());
        }
    }

    /**
     * Send notification when Tier 1 approves (pending → pending_hr)
     */
    public function sendTier1ApprovedNotification(LeaveRequest $leaveRequest, User $approver): void
    {
        try {
            $requester = $leaveRequest->user;

            // 1. Email to staff: "Atasan Anda telah menyetujui, menunggu validasi HR"
            $mailToStaff = new LeaveRequestMail(
                leaveRequest: $leaveRequest,
                action: 'tier1_approved',
                recipient: $requester,
                actor: $approver,
            );
            Mail::to($requester)->send($mailToStaff);

            // 2. Email to HR Manager: "Ada cuti yang perlu divalidasi"
            $hrManager = $this->findHRManager();
            if ($hrManager && $hrManager->id !== $approver->id) {
                $mailToHR = new LeaveRequestMail(
                    leaveRequest: $leaveRequest,
                    action: 'pending_hr',
                    recipient: $hrManager,
                    actor: $approver,
                );
                Mail::to($hrManager)->cc($requester->email)->send($mailToHR);

                Log::info("Sent leave pending_hr email for #{$leaveRequest->id} to HR Manager {$hrManager->email}");
            }

            Log::info("Sent leave tier1_approved email for #{$leaveRequest->id} to {$requester->email}");
        } catch (\Throwable $e) {
            Log::error("Failed to send leave tier1 approved email for #{$leaveRequest->id}: " . $e->getMessage());
        }
    }

    /**
     * Send notification when leave is fully approved
     */
    public function sendApprovedNotification(LeaveRequest $leaveRequest, User $approver): void
    {
        try {
            $requester = $leaveRequest->user;

            $mail = new LeaveRequestMail(
                leaveRequest: $leaveRequest,
                action: 'approved',
                recipient: $requester,
                actor: $approver,
            );

            Mail::to($requester)->send($mail);

            Log::info("Sent leave approved email for #{$leaveRequest->id} to {$requester->email}");
        } catch (\Throwable $e) {
            Log::error("Failed to send leave approved email for #{$leaveRequest->id}: " . $e->getMessage());
        }
    }

    /**
     * Send notification when leave is rejected
     */
    public function sendRejectedNotification(LeaveRequest $leaveRequest, User $rejector): void
    {
        try {
            $requester = $leaveRequest->user;

            $mail = new LeaveRequestMail(
                leaveRequest: $leaveRequest,
                action: 'rejected',
                recipient: $requester,
                actor: $rejector,
            );

            Mail::to($requester)->send($mail);

            Log::info("Sent leave rejected email for #{$leaveRequest->id} to {$requester->email}");
        } catch (\Throwable $e) {
            Log::error("Failed to send leave rejected email for #{$leaveRequest->id}: " . $e->getMessage());
        }
    }

    /**
     * Find the appropriate approver for a leave request
     */
    protected function findApprover(LeaveRequest $leaveRequest): ?User
    {
        $requester = $leaveRequest->user;
        $department = $requester->department;

        // Staff → Supervisor di dept yang sama
        if ($requester->hasRole('staff')) {
            return User::role('supervisor')
                ->where('department', $department)
                ->where('is_active', true)
                ->first()
                // Fallback ke Manager jika tidak ada Supervisor
                ?? User::role('manager')
                    ->where('department', $department)
                    ->where('is_active', true)
                    ->first();
        }

        // Supervisor → Manager di dept yang sama
        if ($requester->hasRole('supervisor')) {
            return User::role('manager')
                ->where('department', $department)
                ->where('is_active', true)
                ->first();
        }

        // Manager → Director sesuai divisi (dari tabel departments)
        if ($requester->hasRole('manager')) {
            $directorRole = \App\Models\Department::directorRoleFor($department);
            if ($directorRole) {
                return User::role($directorRole)
                    ->where('is_active', true)
                    ->first();
            }
        }

        // Director → Presiden Direktur
        if ($requester->hasAnyRole(['direktur_hr', 'direktur_finance', 'direktur_it', 'direktur_sales'])) {
            return User::role('presiden_direktur')
                ->where('is_active', true)
                ->first();
        }

        return null;
    }

    /**
     * Find HR Manager for Tier 2 notification
     */
    protected function findHRManager(): ?User
    {
        return User::role('manager')
            ->where('department', 'Human Resources')
            ->where('is_active', true)
            ->first();
    }
}
