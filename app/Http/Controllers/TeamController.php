<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Display team documents for supervisor
     */
    public function documents(Request $request)
    {
        $user = auth()->user();

        $documents = Document::where('department', $user->department)
            ->with(['user', 'currentApprover'])
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('user_id'), function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('team.documents', compact('documents'));
    }
}
