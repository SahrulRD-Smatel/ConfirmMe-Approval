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

        // Executive dashboard for Board of Directors
        if ($user->department === 'Board of Directors') {
            return redirect()->route('executive.dashboard');
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
