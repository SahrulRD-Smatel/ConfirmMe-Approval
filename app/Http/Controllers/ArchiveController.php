<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    /**
     * Display document archive
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Document::with(['user', 'approvals'])
            ->whereIn('status', ['approved', 'rejected', 'completed', 'paid'])
            // Permission-based filtering
            ->when(!$user->hasPermissionTo('view-all-documents'), function ($q) use ($user) {
                $q->where(function ($query) use ($user) {
                    // Own documents
                    $query->where('user_id', $user->id)
                        // Documents they approved
                        ->orWhereHas('approvals', function ($subQ) use ($user) {
                            $subQ->where('approver_id', $user->id);
                        });

                    // Department documents
                    if ($user->hasPermissionTo('view-department-documents')) {
                        $query->orWhere('department', $user->department);
                    }

                    // Finance documents
                    if ($user->hasPermissionTo('view-finance-documents')) {
                        $query->orWhereIn('approval_type', ['po', 'invoice']);
                    }
                });
            })

            // Filter options
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('approval_type'), function ($q) use ($request) {
                $q->where('approval_type', $request->approval_type);
            })
            ->when($request->filled('from_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from_date);
            })
            ->when($request->filled('to_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->to_date);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('title', 'like', '%' . $request->search . '%')
                        ->orWhere('document_number', 'like', '%' . $request->search . '%');
                });
            })
            ->latest();

        $documents = $query->paginate(20)->withQueryString();

        $stats = [
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
            'completed' => (clone $query)->whereIn('status', ['completed', 'paid'])->count(),
        ];

        return view('archive.index', compact('documents', 'stats'));
    }

    /**
     * Show archived document
     */
    public function show(Document $document)
    {
        $this->authorize('view', $document);

        $document->load([
            'user',
            'approvals.approver',
            'attachments',
            'comments.user',
            'approvalLogs.user',
        ]);

        return view('archive.show', compact('document'));
    }
}
