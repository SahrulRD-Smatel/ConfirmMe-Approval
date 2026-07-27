<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class SidebarServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     * 
     * Shares cached badge counts with sidebar and topbar views
     * to prevent 7+ DB queries on every page load.
     */
    public function boot(): void
    {
        View::composer(['layouts.sidebar', 'layouts.topbar'], function ($view) {
            if (!Auth::check()) {
                return;
            }

            $user = Auth::user();
            $cacheKey = "sidebar_badges_{$user->id}";
            $cacheTtl = 30; // seconds — short enough to be "near real-time"

            $badges = Cache::remember($cacheKey, $cacheTtl, function () use ($user) {
                $data = [
                    'pendingApprovalCount' => 0,
                    'pendingLeaveCount' => 0,
                    'presdirPendingCount' => 0,
                    'presdirLeaveCount' => 0,
                    'pendingPOCount' => 0,
                    'pendingInvoiceCount' => 0,
                    'pendingLoanCount' => 0,
                    'unreadNotifCount' => 0,
                ];

                // Unread notifications count (used in topbar)
                $data['unreadNotifCount'] = $user->unreadNotifications()->count();

                // Pending approvals for this user (documents assigned to them)
                $data['pendingApprovalCount'] = $user->pendingApprovals()->count();

                // Leave request counts — uses current_approver_id (synced with 4-step system)
                $data['pendingLeaveCount'] = $this->getPendingLeaveCount($user);

                // Presiden Direktur specific
                if ($user->hasRole('presiden_direktur')) {
                    $data['presdirPendingCount'] = \App\Models\Document::where('status', 'presiden_review')
                        ->where('current_approver_id', $user->id)
                        ->count();

                    // Count leave requests assigned to presiden via current_approver_id
                    $data['presdirLeaveCount'] = \App\Models\LeaveRequest::where('current_approver_id', $user->id)
                        ->whereIn('status', [
                            'pending', 'pending_supervisor', 'pending_manager',
                            'pending_hrd', 'pending_hr', 'pending_hr_final',
                        ])
                        ->count();
                }

                // Finance specific
                if ($user->hasRole('finance')) {
                    $data['pendingPOCount'] = \App\Models\Document::where('status', 'approved')
                        ->where('approval_type', 'po')
                        ->whereDoesntHave('purchaseOrder')
                        ->count();

                    $data['pendingInvoiceCount'] = \App\Models\Document::where('status', 'approved')
                        ->where('approval_type', 'invoice')
                        ->whereDoesntHave('invoice')
                        ->count();

                    $data['pendingLoanCount'] = \App\Models\EmployeeLoan::where('status', 'pending_disbursement')->count();
                }

                return $data;
            });

            $view->with('sidebarBadges', $badges);
        });
    }

    /**
     * Calculate pending leave count based on user role.
     * Uses current_approver_id as primary source (synced with 4-step approval system).
     * Falls back to role-based query for unassigned leave requests.
     */
    private function getPendingLeaveCount($user): int
    {
        // Primary: count leave requests directly assigned to this user via current_approver_id
        $assignedCount = \App\Models\LeaveRequest::where('current_approver_id', $user->id)
            ->whereIn('status', [
                'pending', 'pending_supervisor', 'pending_manager',
                'pending_hrd', 'pending_hr', 'pending_hr_final',
            ])
            ->where('user_id', '!=', $user->id) // Never count own requests
            ->count();

        // Secondary: count unassigned requests that match role-based fallback
        $unassignedCount = $this->getUnassignedLeaveCount($user);

        return $assignedCount + $unassignedCount;
    }

    /**
     * Count unassigned leave requests that this user could approve based on role.
     * Only counts leave requests where current_approver_id is NULL (not yet assigned).
     */
    private function getUnassignedLeaveCount($user): int
    {
        $baseQuery = \App\Models\LeaveRequest::whereNull('current_approver_id')
            ->where('user_id', '!=', $user->id)
            ->whereIn('status', [
                'pending', 'pending_supervisor', 'pending_manager',
                'pending_hrd', 'pending_hr', 'pending_hr_final',
            ]);

        // Supervisor: staff in same dept with pending_supervisor
        if ($user->hasRole('supervisor')) {
            return (clone $baseQuery)
                ->whereIn('status', ['pending', 'pending_supervisor'])
                ->whereHas('user', fn($u) => $u->where('department', $user->department)->role('staff'))
                ->count();
        }

        // Manager non-HR
        $isHR = $user->department === 'Human Resources';
        if ($user->hasRole('manager') && !$isHR) {
            return (clone $baseQuery)
                ->whereIn('status', ['pending', 'pending_supervisor', 'pending_manager'])
                ->whereHas('user', fn($u) => $u->where('department', $user->department))
                ->count();
        }

        // HR Manager
        if ($user->hasRole('manager') && $isHR) {
            return (clone $baseQuery)
                ->where(function ($q) use ($user) {
                    $q->whereIn('status', ['pending_hr', 'pending_hr_final', 'pending_hrd'])
                        ->where('user_id', '!=', $user->id);
                })
                ->count();
        }

        // HRD Coordinator
        if ($user->hasRole('hrd_coordinator')) {
            return (clone $baseQuery)
                ->whereIn('status', ['pending_hrd'])
                ->count();
        }

        // HRD
        if ($user->hasRole('hrd')) {
            return (clone $baseQuery)
                ->whereIn('status', ['pending_hrd', 'pending_hr', 'pending_hr_final'])
                ->count();
        }

        // Division Directors (Finance, IT, Sales, Operasional)
        if ($user->hasAnyRole(['direktur_finance', 'direktur_it', 'direktur_sales', 'direktur_operasional'])) {
            $deptMap = [
                'direktur_finance' => 'Finance',
                'direktur_it' => 'IT',
                'direktur_sales' => 'Sales',
                'direktur_operasional' => 'Operations',
            ];
            $myRole = $user->getRoleNames()->first(fn($r) => str_starts_with($r, 'direktur_'));
            $targetDept = $deptMap[$myRole] ?? null;
            if ($targetDept) {
                return (clone $baseQuery)
                    ->whereIn('status', ['pending', 'pending_manager'])
                    ->whereHas('user', fn($u) => $u->role('manager')->where('department', $targetDept))
                    ->count();
            }
        }

        // HR Director
        if ($user->hasRole('direktur_hr')) {
            return (clone $baseQuery)
                ->whereIn('status', ['pending', 'pending_manager'])
                ->whereHas('user', fn($u) => $u->role('manager')->where('department', 'Human Resources'))
                ->count();
        }

        // Admin
        if ($user->hasRole('admin')) {
            return (clone $baseQuery)
                ->whereIn('status', ['pending', 'pending_hr', 'pending_hr_final'])
                ->count();
        }

        return 0;
    }
}
