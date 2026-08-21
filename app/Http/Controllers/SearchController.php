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

        // Permission-based scoping
        if (!$user->hasPermissionTo('view-all-documents')) {
            $documentsQuery->where(function ($q) use ($user) {
                // Own documents
                $q->where('user_id', $user->id)
                  // Documents they approved
                  ->orWhereHas('approvals', function ($subQ) use ($user) {
                      $subQ->where('approver_id', $user->id);
                  });

                // Department documents
                if ($user->hasPermissionTo('view-department-documents')) {
                    $q->orWhere('department', $user->department);
                }

                // Finance documents
                if ($user->hasPermissionTo('view-finance-documents')) {
                    $q->orWhereIn('approval_type', ['po', 'invoice'])
                      ->orWhereIn('status', ['finance_review', 'waiting_disbursement', 'paid']);
                }
                
                // Purchasing documents
                if ($user->hasPermissionTo('manage-purchasing')) {
                    $q->orWhere('approval_type', 'po');
                }
            });
        }

        $documents = $documentsQuery
            ->with(['user', 'currentApprover'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('search.index', compact('documents', 'query'));
    }
}
