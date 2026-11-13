<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. System Administrator dashboard (Admin selalu prioritas ke Admin Dashboard)
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
                'labels' => $roles->pluck('name')->map(fn($name) => role_label($name))->toArray(),
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

        // 2. Executive dashboard (Presiden Direktur / Executive Role)
        if ($user->can('menu-executive-dashboard')) {
            return redirect()->route('executive.dashboard');
        }

        // 3. Finance Dedicated Dashboard (Hanya untuk pengelola modul Finance, BUKAN untuk sekadar view-finance-documents)
        if (feature_active('module_finance') && ($user->hasRole('finance') || $user->can('manage-budget'))) {
            return redirect()->route('finance.dashboard');
        }

        // Base stats query based on role
        $documentsQuery = Document::query();

        if ($user->can('view-all-documents')) {
            // View all
        } elseif ($user->can('view-department-documents')) {
            $documentsQuery->where('department', $user->department);
        } elseif ($user->can('view-finance-documents')) {
            $documentsQuery->where(function ($q) use ($user) {
                $q->financial()->orWhere('user_id', $user->id);
            });
        } else {
            $documentsQuery->where('user_id', $user->id);
        }

        // Calculate statistics
        // Anyone who can approve documents is an approver
        $isApprover = $user->can('approve-document');

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
            'my_documents' => Document::where('user_id', $user->id)->count(),
            'my_approved' => Document::where('user_id', $user->id)->where('status', 'approved')->count(),
            'my_pending' => Document::where('user_id', $user->id)->whereIn('status', ['pending', 'in_review', 'partially_approved'])->count(),
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
        $whosOff = collect();
        if (feature_active('module_hris')) {
            $whosOff = \App\Models\LeaveRequest::with('user')
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->get();
        }

        return view('dashboard', compact('stats', 'recentDocuments', 'pendingApprovals', 'announcements', 'whosOff'));
    }
}
