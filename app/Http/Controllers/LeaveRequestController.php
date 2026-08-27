<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Services\LeaveNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeaveRequestController extends Controller
{
    protected LeaveNotificationService $notificationService;
    protected LeaveApprovalController $leaveApprovalController;

    public function __construct(LeaveNotificationService $notificationService, LeaveApprovalController $leaveApprovalController)
    {
        $this->notificationService = $notificationService;
        $this->leaveApprovalController = $leaveApprovalController;
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->user()->hasPermissionTo('menu-my-leaves'), 403, 'Anda tidak memiliki akses ke fitur ini.');
            return $next($request);
        });
    }

    /**
     * Display a listing of the user's leave requests.
     */
    public function index()
    {
        $leaveRequests = auth()->user()->leaveRequests()
            ->with('currentApprover')
            ->latest()
            ->paginate(10);

        return view('leave-requests.index', compact('leaveRequests'));
    }

    /**
     * Show the form for creating a new leave request.
     */
    public function create()
    {
        $user = auth()->user();

        return view('leave-requests.create', [
            'leaveBalance' => $user->effective_annual_leave_balance,
            'sickLeaveUsed' => $user->sick_leave_used ?? 0,
        ]);
    }

    /**
     * Store a newly created leave request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:annual,sick,unpaid,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Calculate duration
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);
        $duration = $startDate->diffInDays($endDate) + 1;

        // Check leave balance for annual leave
        $user = auth()->user();
        if ($validated['type'] === 'annual' && $duration > $user->effective_annual_leave_balance) {
            return back()->withErrors(['type' => 'Sisa cuti tahunan Anda (termasuk yang sedang diproses) tidak mencukupi.'])->withInput();
        }

        // Handle file upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave-attachments', 'public');
        }

        // Create leave request
        $leaveRequest = LeaveRequest::create([
            'user_id' => $user->id,
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'status' => 'pending',
            'attachment' => $attachmentPath,
        ]);

        // Initialize 4-step approval workflow (auto-skip based on requester's role)
        $this->leaveApprovalController->initializeLeaveWorkflow($leaveRequest);

        // Send email notification to approver
        $this->notificationService->sendSubmittedNotification($leaveRequest);

        return redirect()->route('leave-requests.index')
            ->with('success', 'Pengajuan cuti berhasil dikirim dan menunggu persetujuan.');
    }

    /**
     * Display the specified leave request.
     */
    public function show(LeaveRequest $leaveRequest)
    {
        // Ensure user can only view their own requests
        if ((int) $leaveRequest->user_id !== (int) auth()->id()) {
            abort(403, 'Anda hanya dapat melihat pengajuan cuti milik Anda sendiri.');
        }

        return view('leave-requests.show', compact('leaveRequest'));
    }
}
