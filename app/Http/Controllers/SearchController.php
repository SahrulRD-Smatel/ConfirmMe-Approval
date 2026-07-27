<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Search documents with role-based scoping.
     * Ensures users only see documents they have access to.
     */
    public function index(Request $request)
    {
        $query = $request->get('q', '');

        if (empty($query)) {
            return view('search.index', ['documents' => collect(), 'query' => '']);
        }

        $user = auth()->user();

        $documentsQuery = Document::where(function ($q) use ($query) {
            $q->where('title', 'like', '%' . $query . '%')
                ->orWhere('document_number', 'like', '%' . $query . '%')
                ->orWhere('description', 'like', '%' . $query . '%');
        });

        // Role-based scoping — consistent with DocumentPolicy & ArchiveController
        if ($user->hasRole('staff')) {
            // Staff: hanya dokumen milik sendiri
            $documentsQuery->where('user_id', $user->id);
        } elseif ($user->hasRole('supervisor')) {
            // Supervisor: dokumen departemen sendiri
            $documentsQuery->where(function ($q) use ($user) {
                $q->where('department', $user->department)
                    ->orWhere('user_id', $user->id);
            });
        } elseif ($user->hasRole('finance')) {
            // Finance: dokumen PO/Invoice + milik sendiri
            $documentsQuery->where(function ($q) use ($user) {
                $q->whereIn('approval_type', ['po', 'invoice'])
                    ->orWhere('user_id', $user->id)
                    ->orWhereIn('status', ['finance_review', 'approved', 'completed', 'paid', 'waiting_disbursement']);
            });
        } elseif ($user->hasRole('purchasing')) {
            // Purchasing: dokumen PO + milik sendiri
            $documentsQuery->where(function ($q) use ($user) {
                $q->where('approval_type', 'po')
                    ->orWhere('user_id', $user->id);
            });
        } elseif ($user->hasRole('manager') && !$user->hasRole('hrd')) {
            // Manager non-HRD: dokumen departemen sendiri + yang pernah di-approve
            $documentsQuery->where(function ($q) use ($user) {
                $q->where('department', $user->department)
                    ->orWhere('user_id', $user->id)
                    ->orWhereHas('approvals', function ($subQ) use ($user) {
                        $subQ->where('approver_id', $user->id);
                    });
            });
        }
        // Admin, HRD, Manager+HRD, all Directors, Presiden Direktur → can see all documents (no filter)

        $documents = $documentsQuery
            ->with(['user', 'currentApprover'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('search.index', compact('documents', 'query'));
    }
}
