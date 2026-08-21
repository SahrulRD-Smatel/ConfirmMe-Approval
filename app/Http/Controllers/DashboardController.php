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
        if ($user->hasRole('finance')) {
            return redirect()->route('finance.dashboard');
        }

        // Presiden Direktur uses executive dashboard
        if ($user->hasRole('presiden_direktur')) {
            return redirect()->route('executive.dashboard');
        }

        // Base stats query based on role
        $documentsQuery = Document::query();

        if ($user->hasRole('staff')) {
            $documentsQuery->where('user_id', $user->id);
        } elseif ($user->hasRole('supervisor')) {
            $documentsQuery->where('department', $user->department);
        }

        // Calculate statistics
        // Anyone who is not just a staff or admin can potentially be an approver
        $isApprover = !$user->hasAnyRole(['staff', 'admin']);

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
