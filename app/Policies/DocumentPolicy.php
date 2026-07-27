<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class DocumentPolicy
{
    /**
     * Determine whether the user can view any documents.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-document');
    }

    /**
     * Determine whether the user can view the document.
     */
    public function view(User $user, Document $document): Response|bool
    {
        // Check if user has view permission
        if (!$user->hasPermissionTo('view-document')) {
            Log::warning('DocumentPolicy::view denied - missing view-document permission', [
                'user_id' => $user->id,
                'user_roles' => $user->getRoleNames()->toArray(),
                'document_id' => $document->id,
            ]);
            return Response::deny("Akses ditolak: Anda tidak memiliki permission 'view-document'. Role Anda: " . $user->getRoleNames()->implode(', '));
        }

        // Owner can view
        if ((int) $user->id === (int) $document->user_id) {
            return true;
        }

        // Current approver can view
        if ((int) $user->id === (int) ($document->current_approver_id ?? 0)) {
            return true;
        }

        // Admin can view all
        if ($user->hasRole('admin')) {
            return true;
        }

        // Directors can view all (any division director or presiden)
        if ($user->hasAnyRole(['direktur_hr', 'direktur_finance', 'direktur_it', 'direktur_sales', 'direktur_operasional', 'presiden_direktur'])) {
            return true;
        }

        // HRD can view all
        if ($user->hasRole('hrd')) {
            return true;
        }

        // Finance can view documents related to their work:
        // - Documents with approval_type 'po' or 'invoice' (they're part of the workflow)
        // - Documents in finance_review, approved, completed, paid status
        // Note: We don't return false here so that the approvals check below can still catch
        // loan documents that Finance has already approved
        if ($user->hasRole('finance')) {
            if (in_array($document->approval_type, ['po', 'invoice'])) {
                return true;
            }
            if (in_array($document->status, ['finance_review', 'approved', 'completed', 'paid', 'waiting_disbursement'])) {
                return true;
            }
        }

        // Purchasing can view PO documents
        if ($user->hasRole('purchasing') && $document->approval_type === 'po') {
            return true;
        }

        // HRD Coordinator can view leave-related and HR documents
        if ($user->hasRole('hrd_coordinator')) {
            return true;
        }

        // Manager can view documents in their department or in manager_review status
        if ($user->hasRole('manager')) {
            if ($user->department === $document->department) {
                return true;
            }
            if ($document->status === 'manager_review') {
                return true;
            }
        }

        // Supervisor can view team documents
        if ($user->hasRole('supervisor') && $user->department === $document->department) {
            return true;
        }

        // Users who have approved this document
        if ($document->approvals()->where('approver_id', $user->id)->exists()) {
            return true;
        }

        Log::warning('DocumentPolicy::view denied - no matching rule', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_roles' => $user->getRoleNames()->toArray(),
            'user_department' => $user->department,
            'document_id' => $document->id,
            'document_status' => $document->status,
            'document_department' => $document->department,
            'document_approval_type' => $document->approval_type,
            'document_owner_id' => $document->user_id,
            'document_current_approver_id' => $document->current_approver_id,
        ]);

        return Response::deny("Akses ditolak: Anda ({$user->name}, role: " . $user->getRoleNames()->implode(', ') . ", dept: {$user->department}) tidak memiliki akses ke dokumen #{$document->id} (status: {$document->status}, dept: {$document->department}, approver_id: {$document->current_approver_id})");
    }

    /**
     * Determine whether the user can create documents.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-document');
    }

    /**
     * Determine whether the user can update the document.
     */
    public function update(User $user, Document $document): bool
    {
        // Check if user has edit permission
        if (!$user->hasPermissionTo('edit-document')) {
            return false;
        }

        // Only owner can update
        if ((int) $user->id !== (int) $document->user_id) {
            return false;
        }

        // Only draft documents can be updated
        return $document->status === 'draft';
    }

    /**
     * Determine whether the user can delete the document.
     */
    public function delete(User $user, Document $document): bool
    {
        // Check if user has delete permission
        if (!$user->hasPermissionTo('delete-document')) {
            return false;
        }

        // Only owner can delete their own documents
        if ((int) $user->id !== (int) $document->user_id) {
            return false;
        }

        // Only draft documents can be deleted
        return $document->status === 'draft';
    }

    /**
     * Determine whether the user can submit the document for approval.
     */
    public function submit(User $user, Document $document): bool
    {
        // Check if user has submit permission
        if (!$user->hasPermissionTo('submit-document')) {
            return false;
        }

        // Only owner can submit
        if ((int) $user->id !== (int) $document->user_id) {
            return false;
        }

        // Only draft documents can be submitted
        return $document->status === 'draft';
    }

    /**
     * Determine whether the user can approve/reject the document.
     */
    public function approve(User $user, Document $document): Response|bool
    {
        // Check if user has approve permission
        if (!$user->hasPermissionTo('approve-document')) {
            Log::warning('DocumentPolicy::approve denied - missing approve-document permission', [
                'user_id' => $user->id,
                'user_roles' => $user->getRoleNames()->toArray(),
                'document_id' => $document->id,
            ]);
            return Response::deny("Akses ditolak: Anda tidak memiliki permission 'approve-document'. Role Anda: " . $user->getRoleNames()->implode(', '));
        }

        // Only current approver can approve
        if ((int) $user->id !== (int) $document->current_approver_id) {
            $currentApprover = $document->currentApprover;
            $approverInfo = $currentApprover ? "{$currentApprover->name} (ID: {$currentApprover->id})" : "ID: {$document->current_approver_id}";

            Log::warning('DocumentPolicy::approve denied - not current approver', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_roles' => $user->getRoleNames()->toArray(),
                'document_id' => $document->id,
                'document_status' => $document->status,
                'current_approver_id' => $document->current_approver_id,
                'current_approver_name' => $currentApprover?->name,
            ]);
            return Response::deny("Akses ditolak: Anda ({$user->name}) bukan approver saat ini untuk dokumen #{$document->id}. Approver saat ini: {$approverInfo}. Status dokumen: {$document->status}");
        }

        return true;
    }

    /**
     * Determine whether the user can reject the document.
     */
    public function reject(User $user, Document $document): bool
    {
        // Check if user has reject permission
        if (!$user->hasPermissionTo('reject-document')) {
            return false;
        }

        // Only current approver can reject
        return (int) $user->id === (int) $document->current_approver_id;
    }

    /**
     * Determine whether the user can print the document.
     */
    public function print(User $user, Document $document): bool
    {
        // Check if user has print permission
        if (!$user->hasPermissionTo('print-document')) {
            return false;
        }

        // Document must be approved or completed
        if (!in_array($document->status, ['approved', 'completed', 'paid'])) {
            return false;
        }

        // User with print permission can print their documents or approved ones
        return true;
    }

    /**
     * Determine whether the user can restore the document.
     */
    public function restore(User $user, Document $document): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the document.
     */
    public function forceDelete(User $user, Document $document): bool
    {
        return false; // Never allow permanent deletion (audit purposes)
    }
}
