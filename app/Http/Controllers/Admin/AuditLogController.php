<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs.
     */
    public function index(Request $request)
    {
        $logs = AuditLog::with('user')
            ->when($request->filled('event'), function ($q) use ($request) {
                $q->where('event', $request->event);
            })
            ->when($request->filled('user_id'), function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            })
            ->when($request->filled('from_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from_date);
            })
            ->when($request->filled('to_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->to_date);
            })
            ->latest()
            ->paginate(50)
            ->withQueryString();

        $events = AuditLog::distinct('event')->pluck('event');

        return view('admin.audit-logs.index', compact('logs', 'events'));
    }

    /**
     * Display the specified audit log.
     */
    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');
        return view('admin.audit-logs.show', compact('auditLog'));
    }

    /**
     * Export audit logs
     */
    public function export(Request $request)
    {
        $logs = AuditLog::with('user')
            ->when($request->filled('event'), function ($q) use ($request) {
                $q->where('event', $request->event);
            })
            ->when($request->filled('from_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from_date);
            })
            ->when($request->filled('to_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->to_date);
            })
            ->latest()
            ->get();

        // Force English locale for PDF to avoid CJK font issues in DomPDF
        app()->setLocale('en');

        $pdf = Pdf::loadView('admin.audit-logs.export', compact('logs'));

        return $pdf->download('audit-log-' . now()->format('Y-m-d') . '.pdf');
    }
}
