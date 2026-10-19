<?php

namespace App\Services;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Mail\LeaveRequestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Traits\LocaleSwitchable;

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
    use LocaleSwitchable;
    /**
     * Send notification when a leave request is submitted
     */
    public function sendSubmittedNotification(LeaveRequest $leaveRequest): void
    {
        try {
            $requester = $leaveRequest->user;
            $approver = $leaveRequest->currentApprover;

            if (!$approver) {
                Log::warning("No approver found for leave request #{$leaveRequest->id} by {$requester->name}");
                return;
            }

            $this->withRecipientLocale($approver, function () use ($leaveRequest, $approver, $requester) {
                $mail = new LeaveRequestMail(
                    leaveRequest: $leaveRequest,
                    action: 'submitted',
                    recipient: $approver,
                    actor: $requester,
                );

                Mail::to($approver)->send($mail);
            });

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
            $this->withRecipientLocale($requester, function () use ($leaveRequest, $requester, $approver) {
                $mailToStaff = new LeaveRequestMail(
                    leaveRequest: $leaveRequest,
                    action: 'tier1_approved',
                    recipient: $requester,
                    actor: $approver,
                );
                Mail::to($requester)->send($mailToStaff);
            });

            // 2. Email to HR Manager: "Ada cuti yang perlu divalidasi"
            $hrManager = $this->findHRManager();
            if ($hrManager && $hrManager->id !== $approver->id) {
                $this->withRecipientLocale($hrManager, function () use ($leaveRequest, $hrManager, $approver, $requester) {
                    $mailToHR = new LeaveRequestMail(
                        leaveRequest: $leaveRequest,
                        action: 'pending_hr',
                        recipient: $hrManager,
                        actor: $approver,
                    );
                    Mail::to($hrManager)->cc($requester->email)->send($mailToHR);
                });

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

            $this->withRecipientLocale($requester, function () use ($leaveRequest, $requester, $approver) {
                $mail = new LeaveRequestMail(
                    leaveRequest: $leaveRequest,
                    action: 'approved',
                    recipient: $requester,
                    actor: $approver,
                );

                Mail::to($requester)->send($mail);
            });

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

            $this->withRecipientLocale($requester, function () use ($leaveRequest, $requester, $rejector) {
                $mail = new LeaveRequestMail(
                    leaveRequest: $leaveRequest,
                    action: 'rejected',
                    recipient: $requester,
                    actor: $rejector,
                );

                Mail::to($requester)->send($mail);
            });

            Log::info("Sent leave rejected email for #{$leaveRequest->id} to {$requester->email}");
        } catch (\Throwable $e) {
            Log::error("Failed to send leave rejected email for #{$leaveRequest->id}: " . $e->getMessage());
        }
    }

    /**
     * Delete findApprover as it's no longer used.
     */

    /**
     * Find HR for Tier 2 notification
     */
    protected function findHRManager(): ?User
    {
        try {
            if (\Spatie\Permission\Models\Permission::where('name', 'manage-attendance')->exists()) {
                $user = User::permission('manage-attendance')
                    ->where('department', 'Human Resources')
                    ->where('is_active', true)
                    ->first()
                    ?? User::permission('manage-attendance')
                        ->where('is_active', true)
                        ->first();
                if ($user) {
                    return $user;
                }
            }
        } catch (\Throwable $e) {}

        try {
            return User::role(['hrd', 'manager'])
                ->where('department', 'Human Resources')
                ->where('is_active', true)
                ->first();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
