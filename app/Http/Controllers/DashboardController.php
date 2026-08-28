<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Finance role uses integrated finance dashboard
        if ($user->hasPermissionTo('manage-budget') || $user->hasPermissionTo('view-finance-documents')) {
            return redirect()->route('finance.dashboard');
        }

        // Executive dashboard
        if ($user->hasRole('presiden_direktur')) {
            return redirect()->route('executive.dashboard');
        }

        // System Administrator dashboard
        if ($user->hasRole('admin')) {
            $adminStats = [
                'total_users' => \App\Models\User::count(),
                'users_growth' => \App\Models\User::whereMonth('created_at', now()->month)->count(),
                'total_departments' => \App\Models\Department::count(),
                'total_roles' => \Spatie\Permission\Models\Role::count(),
                'active_features' => \Illuminate\Support\Facades\DB::table('system_features')->where('is_active', true)->count(),
                'total_features' => \Illuminate\Support\Facades\DB::table('system_features')->count(),
            ];

            // Traffic Proxy: Audit Logs
            $todayLogs = \App\Models\AuditLog::whereDate('created_at', today())->count();
            $yesterdayLogs = \App\Models\AuditLog::whereDate('created_at', today()->subDay())->count();

            // Recent Users
            $recentUsers = \App\Models\User::with('roles')->latest()->take(5)->get();

            // Role Distribution
            $roles = \Spatie\Permission\Models\Role::withCount('users')->get();
            $roleDistribution = [
                'labels' => $roles->pluck('name')->map(fn($name) => ucwords(str_replace('_', ' ', $name)))->toArray(),
                'data' => $roles->pluck('users_count')->toArray(),
            ];

            // Activity Chart (Last 7 days)
            $activityData = [];
            $activityLabels = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = today()->subDays($i);
                $activityLabels[] = $date->format('d M');
                $activityData[] = \App\Models\AuditLog::whereDate('created_at', $date)->count();
            }
            $trafficChart = [
                'labels' => $activityLabels,
                'data' => $activityData,
            ];

            return view('admin.dashboard', compact('adminStats', 'todayLogs', 'yesterdayLogs', 'recentUsers', 'roleDistribution', 'trafficChart'));
        }

        // Base stats query based on role
        $documentsQuery = Document::query();

        if ($user->hasPermissionTo('view-all-documents')) {
            // View all
        } elseif ($user->hasPermissionTo('view-department-documents')) {
            $documentsQuery->where('department', $user->department);
        } else {
            $documentsQuery->where('user_id', $user->id);
        }

        // Calculate statistics
        // Anyone who can approve documents is an approver
        $isApprover = $user->hasPermissionTo('approve-document');

        $stats = [
            'total_documents' => $documentsQuery->count(),
            'pending_review' => $isApprover
                ? Document::where('current_approver_id', $user->id)->count()
                : 0,
            'in_progress' => $documentsQuery->clone()
                ->whereIn('status', ['pending', 'in_review', 'partially_approved'])
                ->count(),
            'approved_this_month' => $documentsQuery->clone()
                ->whereMonth('approved_at', now()->month)
                ->whereYear('approved_at', now()->year)
                ->count(),
            'rejected_this_month' => $documentsQuery->clone()
                ->whereMonth('rejected_at', now()->month)
                ->whereYear('rejected_at', now()->year)
                ->count(),
            'is_approver' => $isApprover,
        ];

        // Recent documents
        $recentDocuments = $documentsQuery->clone()
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        // Pending approvals for approvers
        $pendingApprovals = collect();
        if ($isApprover) {
            $pendingApprovals = Document::where('current_approver_id', $user->id)
                ->with('user')
                ->latest()
                ->take(5)
                ->get();
        }

        // [HRIS Feature] Fetch Active Announcements
        $announcements = \App\Models\Announcement::active()->latest()->take(5)->get();

        // [HRIS Feature] Who's Off Today
        $whosOff = \App\Models\LeaveRequest::with('user')
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->get();

        return view('dashboard', compact('stats', 'recentDocuments', 'pendingApprovals', 'announcements', 'whosOff'));
    }
}
