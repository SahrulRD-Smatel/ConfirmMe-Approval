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
            // Role-based filtering
            ->when($user->hasRole('staff'), function ($q) use ($user) {
                // Staff only sees their own documents
                $q->where('user_id', $user->id);
            })
            ->when($user->hasRole('finance'), function ($q) {
                // Finance sees PO/Invoice type documents
                $q->whereIn('approval_type', ['po', 'invoice']);
            })

            ->when($user->hasRole('supervisor') || ($user->hasRole('manager') && !$user->hasRole('hrd')), function ($q) use ($user) {
                // Manager biasa: hanya lihat dokumen departemen sendiri + yang pernah dia approve
                $q->where(function ($query) use ($user) {
                    $query->where('department', $user->department)
                        ->orWhereHas('approvals', function ($subQ) use ($user) {
                            $subQ->where('approver_id', $user->id);
                        });
                });
            })
            ->when($user->roles->pluck('name')->contains(fn($role) => str_starts_with($role, 'direktur_')), function ($q) use ($user) {
                // Direktur departemen: lihat dokumen departemen sendiri + yang pernah di-approve
                $q->where(function ($query) use ($user) {
                    $query->where('department', $user->department)
                        ->orWhereHas('approvals', function ($subQ) use ($user) {
                            $subQ->where('approver_id', $user->id);
                        });
                });
            })
            ->when($user->hasRole('manager') && $user->hasRole('hrd'), function ($q) {
                // Manager HRD (kombinasi role manager + hrd): lihat semua dokumen
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
