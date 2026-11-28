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
        return $user->can('view-document')
            || $user->can('view-all-documents')
            || $user->can('view-department-documents')
            || $user->can('view-finance-documents')
            || $user->can('menu-archive');
    }

    /**
     * Determine whether the user can view the document.
     */
    public function view(User $user, Document $document): Response|bool
    {
        // 1. Global view access (Admin, HRD, Directors with view-all-documents)
        if ($user->can('view-all-documents')) {
            return true;
        }

        // 2. Owner can always view their own document
        if ((int) $user->id === (int) $document->user_id) {
            return true;
        }

        // 3. Current approver can view
        if ((int) $user->id === (int) ($document->current_approver_id ?? 0)) {
            return true;
        }

        // 4. Users who have approved/acted on this document in history
        if ($document->approvals()->where('approver_id', $user->id)->exists()) {
            return true;
        }

        // 5. Pihak Mengetahui (Tembusan / Observers) listed in template - only accessible once document is legal/approved
        if (in_array($document->status, ['approved', 'completed', 'paid']) && $document->getNotifyUsers()->contains('id', $user->id)) {
            return true;
        }

        // 6. Department-level view access (Manager, Supervisor within same department)
        if ($user->can('view-department-documents') && !empty($user->department) && $user->department === $document->department) {
            return true;
        }

        // 7. Finance-specific view access (Can view any financial document across all departments)
        if ($user->can('view-finance-documents') && $document->isFinancial()) {
            return true;
        }

        // 8. Feature-specific access (Purchasing)
        if ($user->can('manage-purchasing') && in_array($document->approval_type, ['po', 'po_internal', 'purchase_request'])) {
            return true;
        }

        // 9. Standard view-document permission (allows viewing within user's department)
        if ($user->can('view-document') && !empty($user->department) && $user->department === $document->department) {
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
        try {
            return $user->can('create-document');
        } catch (\Throwable $e) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the document.
     */
    public function update(User $user, Document $document): bool
    {
        // 1. Current active approver can adjust details before approving ONLY if explicitly permitted
        if ($document->canApproverEdit($user)) {
            return true;
        }

        // Finalized / locked documents can never be edited (approved, completed, paid, disbursed, repaying, etc.)
        if ($document->isFinalized()) {
            return false;
        }

        // Only documents in editable states (draft, revision, pending_requester_confirmation) can be edited
        if (!$document->canEdit()) {
            return false;
        }

        // Check if user has edit permission or is admin
        try {
            if (!$user->can('edit-document') && !$user->hasRole('admin')) {
                return false;
            }
        } catch (\Throwable $e) {}

        // User must be owner, or have manage-documents, or be admin
        if ((int) $user->id === (int) $document->user_id || $user->hasRole('admin') || $user->can('manage-documents')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the document.
     */
    public function delete(User $user, Document $document): bool
    {
        // Only draft or rejected documents can be deleted (never in-progress, approved, completed, or paid documents)
        if (!$document->canDelete()) {
            return false;
        }

        // Check if user has delete permission or is admin
        try {
            if (!$user->can('delete-document') && !$user->hasRole('admin')) {
                return false;
            }
        } catch (\Throwable $e) {}

        // Owner or admin/manage-documents can delete draft/rejected documents
        return (int) $user->id === (int) $document->user_id || $user->hasRole('admin') || $user->can('manage-documents');
    }

    /**
     * Determine whether the user can submit the document for approval.
     */
    public function submit(User $user, Document $document): bool
    {
        // Only draft, revision, or pending confirmation documents can be submitted
        if (!$document->canSubmit()) {
            return false;
        }

        // Check if user has submit permission or is admin
        if (!$user->can('submit-document') && !$user->hasRole('admin')) {
            return false;
        }

        // Only owner or admin can submit
        return (int) $user->id === (int) $document->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can approve/reject the document.
     */
    public function approve(User $user, Document $document): Response|bool
    {
        // Document must be in pending status to be approved
        if ($document->status !== 'pending') {
            return Response::deny("Akses ditolak: Dokumen #{$document->id} saat ini berstatus '{$document->status}', tidak dapat disetujui lagi.");
        }

        // Check if user has approve permission
        if (!$user->can('approve-document') && !$user->hasRole('admin')) {
            Log::warning('DocumentPolicy::approve denied - missing approve-document permission', [
                'user_id' => $user->id,
                'user_roles' => $user->getRoleNames()->toArray(),
                'document_id' => $document->id,
            ]);
            return Response::deny("Akses ditolak: Anda tidak memiliki permission 'approve-document'. Role Anda: " . $user->getRoleNames()->implode(', '));
        }

        // Only current approver or admin/user with override permission can approve
        if ((int) $user->id !== (int) $document->current_approver_id) {
            if ($user->hasRole('admin') || $user->can('manage-users') || $user->can('view-all-documents')) {
                return true;
            }

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
        if ($document->status !== 'pending') {
            return false;
        }

        // Check if user has reject permission
        if (!$user->can('reject-document') && !$user->hasRole('admin')) {
            return false;
        }

        // Current approver or admin/user with override permission can reject
        return (int) $user->id === (int) $document->current_approver_id || $user->hasRole('admin') || $user->can('manage-users') || $user->can('view-all-documents');
    }

    /**
     * Determine whether the user can print the document.
     */
    public function print(User $user, Document $document): bool
    {
        // Check if user has print permission
        if (!$user->can('print-document') && !$user->hasRole('admin')) {
            return false;
        }

        // Document must be approved, completed, paid, or in active loan lifecycle
        if (!in_array($document->status, ['approved', 'completed', 'paid', 'paid_off', 'waiting_disbursement', 'disbursed', 'repaying'])) {
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
        return $user->can('view-audit-log');
    }

    /**
     * Determine whether the user can permanently delete the document.
     */
    public function forceDelete(User $user, Document $document): bool
    {
        return false; // Never allow permanent deletion (audit purposes)
    }
}
