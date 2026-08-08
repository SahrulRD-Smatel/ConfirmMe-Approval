<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Department;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveReportController extends Controller
{
    /**
     * Display the leave report.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Only admin, HRD, HRD coordinator, or directors can access
        if (!$user->hasAnyRole(['admin', 'hrd', 'hrd_coordinator', 'direktur_hr', 'presiden_direktur', 'manager'])) {
            abort(403, 'Anda tidak memiliki akses ke laporan cuti.');
        }

        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);
        $department = $request->get('department');
        $type = $request->get('type');

        $query = LeaveRequest::with(['user', 'currentApprover'])
            ->where('status', 'approved');

        // Filter by month and year (check if leave overlaps with the month)
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

        $query->where('start_date', '<=', $endOfMonth->format('Y-m-d'))
              ->where('end_date', '>=', $startOfMonth->format('Y-m-d'));

        // Filter by department
        if ($department) {
            $query->whereHas('user', function ($q) use ($department) {
                $q->where('department', $department);
            });
        } elseif ($user->hasRole('manager') && !$user->hasAnyRole(['admin', 'hrd', 'direktur_hr']) && $user->department !== 'Finance') {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('department', $user->department);
            });
        }

        // Filter by leave type
        if ($type) {
            $query->where('type', $type);
        }

        $leaveRequests = $query->orderBy('start_date', 'desc')->paginate(15);
        $leaveRequests->appends($request->query());

        // Get distinct departments
        $departments = \App\Models\User::where('is_active', true)
            ->distinct()
            ->pluck('department')
            ->filter()
            ->sort();

        return view('leave-reports.index', compact('leaveRequests', 'month', 'year', 'department', 'type', 'departments'));
    }
}
