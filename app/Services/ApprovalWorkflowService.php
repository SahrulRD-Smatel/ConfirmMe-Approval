<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentApproval;
use App\Models\EmployeeLoan;
use App\Models\User;
use App\Models\WorkflowChain;
use App\Models\WorkflowSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ApprovalWorkflowService
{
    /**
     * Get role hierarchy levels from DB, fallback to hardcoded defaults.
     * Higher number = higher authority.
     */
    protected function getRoleHierarchy(): array
    {
        return WorkflowSetting::getRoleHierarchy();
    }

    /**
     * Get functional roles from DB — roles that should NOT be skipped by auto-skip.
     */
    protected function getFunctionalRoles(): array
    {
        return WorkflowSetting::getFunctionalRoles();
    }
    /**
     * Status mapping for each step (from config)
     */
    protected array $statusMapping;

    /**
     * Email notification service
     */
    protected EmailNotificationService $emailNotificationService;

    public function __construct(?EmailNotificationService $emailNotificationService = null)
    {
        $this->emailNotificationService = $emailNotificationService ?? new EmailNotificationService();
        $this->statusMapping = config('workflow.status_mapping', [
            'supervisor' => 'supervisor_review',
            'manager' => 'manager_review',
            'hrd' => 'hrd_review',
            'direktur' => 'director_review',
            'finance' => 'finance_review',
            'presiden_direktur' => 'presiden_review',
        ]);
    }

    /**
     * Initialize workflow for a document
     */
    public function initializeWorkflow(Document $document): void
    {
        $workflow = $this->getWorkflowForDocument($document);
        $requesterLevel = $this->getRequesterLevel($document->user);

        $order = 1;
        $assignedApproverIds = []; // Track already-assigned approvers to prevent duplicates
        $hierarchy = $this->getRoleHierarchy();
        $functionalRoles = $this->getFunctionalRoles();

        foreach ($workflow as $role) {


            // Skip approval steps at or below requester's role level
            // Exception: functional roles (finance, hrd, purchasing) and all direktur/presiden roles are never skipped
            if (!in_array($role, $functionalRoles) && !str_contains($role, 'direktur')) {
                $stepLevel = $hierarchy[$role] ?? 0;
                if ($stepLevel > 0 && $stepLevel <= $requesterLevel) {
                    continue;
                }
            }

            // Find an approver with this role (excludes requester + already-assigned approvers)
            $approver = $this->findApproverForRole($role, $document, $assignedApproverIds);

            if ($approver) {
                DocumentApproval::create([
                    'document_id' => $document->id,
                    'approver_id' => $approver->id,
                    'approver_role' => $role,
                    'approval_order' => $order,
                    'status' => 'pending',
                ]);
                $assignedApproverIds[] = $approver->id;
                $order++;
            }
        }

        // Set current approver to first in line
        $firstApproval = $document->approvals()->orderBy('approval_order')->first();
        if ($firstApproval) {
            $document->update([
                'current_approver_id' => $firstApproval->approver_id,
                'status' => $this->statusMapping[$firstApproval->approver_role] ?? 'pending',
            ]);

            // Send notification to first approver
            $this->emailNotificationService->sendApprovalNotification($document, 'pending_approval');
        } else {
            // No approval steps remaining (e.g. manager creates 'approval only' doc)
            // Auto-approve the document
            $this->completeDocument($document);
        }
    }

    /**
     * Check if document is HR-related (for conditional HRD approval)
     */
    protected function isHRRelatedDocument(Document $document): bool
    {
        $keywords = WorkflowSetting::getHrdKeywords();

        $searchText = Str::lower($document->title . ' ' . $document->description);

        foreach ($keywords as $keyword) {
            if (Str::contains($searchText, Str::lower($keyword))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Process approval action
     */
    public function processApproval(
        Document $document,
        User $approver,
        string $action,
        ?string $comment = null,
        ?float $approvedAmount = null
    ): void {
        // Find current approval step
        $currentApproval = DocumentApproval::where('document_id', $document->id)
            ->where('approver_id', $approver->id)
            ->where('status', 'pending')
            ->first();

        if (!$currentApproval) {
            throw new \Exception('No pending approval found for this user');
        }

        if ($action === 'approved') {
            // Any approver in the dynamic chain can technically adjust the amount now.
            // If the template involves finance, they will adjust it. If it involves a director, they can adjust it.
            $finalAmount = $approvedAmount !== null ? (float) $approvedAmount : null;
            $currentApproval->approve($comment, $finalAmount);

            // Check if amount was changed — needs requester confirmation
            if ($finalAmount !== null && (float)$finalAmount !== (float)$document->estimated_budget) {
                $this->sendToRequesterConfirmation($document, $approver, $finalAmount);
            } else {
                $this->moveToNextStep($document);
            }
        } elseif ($action === 'revision') {
            // Revision: return document to requester for edits
            $this->processRevision($document, $approver, $comment);
        } else {
            $currentApproval->reject($comment);
            $this->rejectDocument($document, $comment);
        }

        // Invalidate sidebar badge cache for current approver
        Cache::forget("sidebar_badges_{$approver->id}");

        // Also invalidate for the next approver (if any) so their count updates
        $document->refresh();
        if ($document->current_approver_id) {
            Cache::forget("sidebar_badges_{$document->current_approver_id}");
        }
    }

    /**
     * Send document back to requester for amount confirmation.
     * Called when an approver (Finance) changes the approved amount.
     * The requester can accept or reject the new amount.
     */
    public function sendToRequesterConfirmation(Document $document, User $approver, float $newAmount): void
    {
        // Save original budget if not yet saved
        if ($document->original_budget === null) {
            $document->original_budget = $document->estimated_budget;
        }

        $document->update([
            'status' => 'pending_requester_confirmation',
            'confirmed_amount' => $newAmount,
            'amount_changed_by' => $approver->id,
            'amount_changed_at' => now(),
            'current_approver_id' => null, // No approver — waiting for requester
        ]);

        // Send notification to requester
        $this->emailNotificationService->sendApprovalNotification($document, 'amount_changed');
    }

    /**
     * Process requester's response to the changed amount.
     *
     * @param string $action 'accept' or 'reject'
     */
    public function processRequesterConfirmation(Document $document, string $action): void
    {
        if ($document->status !== 'pending_requester_confirmation') {
            throw new \Exception('Document is not waiting for requester confirmation');
        }

        if ($action === 'accept') {
            // Requester accepts the revised amount — update budget and continue flow
            $document->update([
                'estimated_budget' => $document->confirmed_amount,
                'approved_budget' => $document->confirmed_amount,
                'confirmed_amount' => null,
                'amount_changed_by' => null,
                'amount_changed_at' => null,
            ]);

            // Continue to next approval step
            $this->moveToNextStep($document);
        } else {
            // Requester rejects — cancel/reject the document
            $originalAmount = number_format($document->original_budget ?? $document->estimated_budget, 0, ',', '.');
            $changedAmount = number_format($document->confirmed_amount, 0, ',', '.');

            $this->rejectDocument(
                $document,
                "Nominal ditolak oleh requester. Pengajuan: Rp {$originalAmount}, Disetujui: Rp {$changedAmount}. Requester memilih untuk tidak melanjutkan."
            );
        }
    }

    /**
     * Process revision request — return document to requester for edits.
     * The document goes back to 'revision' status.
     * When re-submitted, it returns to the approver who requested the revision.
     */
    public function processRevision(Document $document, User $approver, ?string $comment = null): void
    {
        // Mark current approval as 'revision_requested'
        $currentApproval = DocumentApproval::where('document_id', $document->id)
            ->where('approver_id', $approver->id)
            ->where('status', 'pending')
            ->first();

        if ($currentApproval) {
            $currentApproval->update([
                'status' => 'revision_requested',
                'comment' => $comment,
                'action_at' => now(),
            ]);
        }

        // Save original budget if this is the first revision
        if ($document->revision_count === 0 && $document->original_budget === null) {
            $document->original_budget = $document->estimated_budget;
        }

        $document->update([
            'status' => 'revision',
            'revision_notes' => $comment,
            'revision_count' => $document->revision_count + 1,
            'revision_requested_by' => $approver->id,
            'revision_requested_at' => now(),
            'current_approver_id' => null,
        ]);

        // Send notification to requester
        $this->emailNotificationService->sendApprovalNotification($document, 'revision_requested');
    }

    /**
     * Re-submit a document after revision.
     * Returns to the approver who requested the revision, NOT from the beginning.
     */
    public function resubmitAfterRevision(Document $document): void
    {
        // Find the approval step that requested revision
        $revisionApproval = DocumentApproval::where('document_id', $document->id)
            ->where('status', 'revision_requested')
            ->orderBy('approval_order')
            ->first();

        if ($revisionApproval) {
            // Reset this step back to pending
            $revisionApproval->update([
                'status' => 'pending',
                'comment' => null,
                'action_at' => null,
            ]);

            // Set current approver back to the revision requester
            $document->update([
                'status' => $this->statusMapping[$revisionApproval->approver_role] ?? 'pending',
                'current_approver_id' => $revisionApproval->approver_id,
                'revision_notes' => null,
                'revision_requested_by' => null,
                'revision_requested_at' => null,
            ]);

            // Invalidate sidebar badge cache for the approver
            Cache::forget("sidebar_badges_{$revisionApproval->approver_id}");

            // Send notification to the approver that document has been revised
            $this->emailNotificationService->sendApprovalNotification($document, 'pending_approval');
        } else {
            // Fallback: re-initialize workflow if no revision step found
            $this->initializeWorkflow($document);
        }
    }

    /**
     * Move document to next approval step
     */
    protected function moveToNextStep(Document $document): void
    {
        // Find next pending approval
        $nextApproval = DocumentApproval::where('document_id', $document->id)
            ->where('status', 'pending')
            ->orderBy('approval_order')
            ->first();

        if ($nextApproval) {
            // Move to next step
            $document->update([
                'current_approver_id' => $nextApproval->approver_id,
                'status' => $this->statusMapping[$nextApproval->approver_role] ?? 'pending',
            ]);

            // Send notification to next approver (TO) + CC previous approvers
            $this->emailNotificationService->sendApprovalNotification($document, 'pending_approval');
        } else {
            // All approvals completed
            $this->completeDocument($document);
        }
    }

    /**
     * Complete document after all approvals
     */
    protected function completeDocument(Document $document): void
    {
        // Determine final status based on document type:
        // - only: APPROVED (complete)
        // - po: APPROVED → User can generate PO
        // - invoice: APPROVED → Ready to Pay
        // - loan: WAITING_DISBURSEMENT → Finance needs to disburse funds
        $status = $document->approval_type === 'loan' ? 'waiting_disbursement' : 'approved';

        // Calculate final approved budget (use minimum if multiple amounts provided)
        $approvedBudget = null;
        if (config('workflow.requires_nominal.' . $document->approval_type, false)) {
            $approvedBudget = $document->approvals()
                ->whereNotNull('approved_amount')
                ->min('approved_amount') ?? $document->estimated_budget;
        }

        // For LOAN type: use finance-approved amount if available, otherwise fallback to metadata loan_amount
        if ($document->approval_type === 'loan') {
            $financeApprovedAmount = $document->approvals()
                ->where('approver_role', 'finance')
                ->where('status', 'approved')
                ->whereNotNull('approved_amount')
                ->value('approved_amount');

            if ($financeApprovedAmount !== null) {
                // Finance has adjusted the loan amount - use their approved amount
                $approvedBudget = (float) $financeApprovedAmount;
            } elseif (isset($document->metadata['loan_amount']) && $approvedBudget === null) {
                // No finance adjustment - fallback to original loan_amount from metadata
                $approvedBudget = (float) $document->metadata['loan_amount'];
            }
        }

        $document->update([
            'status' => $status,
            'current_approver_id' => null,
            'approved_budget' => $approvedBudget,
            'approved_at' => now(),
        ]);

        // Generate approval hash for QR code
        $document->generateApprovalHash();

        // Commit budget for PO type documents
        if (in_array($document->approval_type, ['po', 'po_internal']) && $approvedBudget > 0) {
            $this->commitBudget($document, $approvedBudget);
        }

        // Create EmployeeLoan record for LOAN type documents
        if ($document->approval_type === 'loan') {
            $this->createEmployeeLoan($document, $approvedBudget);
        }

        // Send notification to requester that document is approved
        $this->emailNotificationService->sendApprovalNotification($document, 'completed');
    }

    /**
     * Create Employee Loan record after loan document is approved
     */
    protected function createEmployeeLoan(Document $document, ?float $approvedAmount): void
    {
        $loanAmount = $approvedAmount ?? $document->estimated_budget ?? 0;

        // Default 12 months if not specified in metadata
        $installmentMonths = $document->metadata['installment_months'] ?? 12;

        // Calculate monthly installment (rounded UP to nearest 1000 for easier payments)
        // Example: 10,000,000 / 12 = 833,333.33 → ceil to 834,000
        $rawMonthly = $loanAmount / $installmentMonths;
        $monthlyInstallment = ceil($rawMonthly / 1000) * 1000;

        // Calculate last installment to cover the remainder (will be smaller)
        // Last = Total - (Monthly × (Months - 1))
        $lastInstallment = $loanAmount - ($monthlyInstallment * ($installmentMonths - 1));

        // If last installment is negative (overpaid from rounding), adjust to 0
        // and reduce monthly accordingly
        if ($lastInstallment <= 0) {
            $monthlyInstallment = floor($loanAmount / $installmentMonths / 1000) * 1000;
            $lastInstallment = $loanAmount - ($monthlyInstallment * ($installmentMonths - 1));
        }

        EmployeeLoan::create([
            'document_id' => $document->id,
            'employee_id' => $document->user_id,
            'loan_amount' => $loanAmount,
            'installment_months' => $installmentMonths,
            'monthly_installment' => $monthlyInstallment,
            'last_installment' => $lastInstallment,
            'outstanding_amount' => $loanAmount,
            'status' => 'pending_disbursement',
            'bank_account_name' => $document->metadata['bank_account_name'] ?? null,
            'bank_account_number' => $document->metadata['bank_account_number'] ?? null,
            'bank_name' => $document->metadata['bank_name'] ?? null,
            'created_by' => $document->user_id,
        ]);
    }

    /**
     * Commit budget when PO is approved
     */
    protected function commitBudget(Document $document, float $amount): void
    {
        $budget = \App\Models\Budget::active()
            ->forYear(date('Y'))
            ->forDepartment($document->department)
            ->first();

        if ($budget && $budget->hasSufficientFunds($amount)) {
            $budget->increment('committed_budget', $amount);
        }
    }

    /**
     * Reject document
     */
    public function rejectDocument(Document $document, string $reason): void
    {
        // Mark remaining approvals as skipped
        DocumentApproval::where('document_id', $document->id)
            ->where('status', 'pending')
            ->update(['status' => 'skipped']);

        $document->update([
            'status' => 'rejected',
            'current_approver_id' => null,
            'rejection_reason' => $reason,
            'rejected_at' => now(),
        ]);

        // Send notification to requester that document is rejected
        $this->emailNotificationService->sendApprovalNotification($document, 'rejected');
    }

    /**
     * Auto-reject all pending documents using a specific template code.
     * Called when a template's workflow is modified or deleted.
     */
    public function autoRejectPendingDocumentsForTemplate(string $templateCode, string $reason): void
    {
        // Get all pending documents with this template code
        // Pending statuses: supervisor_review, manager_review, etc.
        $pendingDocuments = Document::where('template', $templateCode)
            ->whereNotIn('status', ['approved', 'rejected', 'waiting_disbursement', 'completed', 'cancelled'])
            ->get();

        foreach ($pendingDocuments as $document) {
            $this->rejectDocument($document, $reason);
        }
    }

    /**
     * Get workflow for a specific document
     */
    protected function getWorkflowForDocument(Document $document): array
    {
        if ($document->template) {
            $template = \App\Models\DocumentTemplate::withTrashed()->where('code', $document->template)->first();
            if ($template && !empty($template->workflow)) {
                return $template->workflow;
            }
        }

        // Fallback or throw error if workflow cannot be resolved
        throw new \Exception("Workflow tidak ditemukan untuk dokumen ini. Harap periksa konfigurasi template.");
    }

    /**
     * Find approver for a specific role
     * @param array $excludeIds IDs of users already assigned as approvers (to prevent duplicates)
     */
    protected function findApproverForRole(string $role, Document $document, array $excludeIds = []): ?User
    {
        $requesterId = $document->user_id;
        $allExcluded = array_merge([$requesterId], $excludeIds);

        // 1. Handle specific user assignment (e.g. user:15)
        if (str_starts_with($role, 'user:')) {
            $userId = (int) substr($role, 5);
            return User::where('id', $userId)
                ->where('is_active', true)
                ->first(); // We don't exclude specific users because it's explicitly requested
        }

        // Backward compatibility and clean up role string
        if (str_starts_with($role, 'role:')) {
            $role = substr($role, 5);
        }

        // 1.5 Handle 'direktur' meta-role - dynamically map to division-specific director
        if ($role === 'direktur') {
            return $this->findDivisionDirector($document, $excludeIds);
        }

        // 2. Try to find someone with the requested role IN THE SAME DEPARTMENT as the requester
        // This is crucial for roles like 'manager' or 'supervisor' to ensure it goes to the right division
        $approverInSameDept = User::role($role)
            ->where('department', $document->department)
            ->where('is_active', true)
            ->whereNotIn('id', $allExcluded)
            ->first();

        if ($approverInSameDept) {
            return $approverInSameDept;
        }

        // 3. Fallback: Find ANY active user with this role globally
        // This handles cross-department roles (like 'direktur_hr', 'purchasing', 'hrd_coordinator')
        $approver = User::role($role)
            ->where('is_active', true)
            ->whereNotIn('id', $allExcluded)
            ->first();

        // 4. Ultimate Fallback: if no alternative found, allow duplicate (better than breaking the flow)
        if (!$approver && !empty($excludeIds)) {
            $approver = User::role($role)
                ->where('is_active', true)
                ->where('id', '!=', $requesterId)
                ->first();
        }

        return $approver;
    }

    /**
     * Get the hierarchy level of the document requester.
     * Returns the highest level among all roles the user has.
     */
    protected function getRequesterLevel(User $user): int
    {
        $maxLevel = 1; // default: staff level
        $hierarchy = $this->getRoleHierarchy();

        foreach ($hierarchy as $role => $level) {
            if ($user->hasRole($role)) {
                $maxLevel = max($maxLevel, $level);
            }
        }

        return $maxLevel;
    }

    /**
     * Find the appropriate division director for a document
     */
    protected function findDivisionDirector(Document $document, array $excludeIds = []): ?User
    {
        $requesterId = $document->user_id;
        $allExcluded = array_merge([$requesterId], $excludeIds);

        // Get division from document's department
        $division = $document->department;

        // Get the director role from departments table (dynamic)
        $directorRole = \App\Models\Department::directorRoleFor($division);

        if ($directorRole) {
            $director = User::role($directorRole)
                ->where('is_active', true)
                ->whereNotIn('id', $allExcluded)
                ->first();

            if ($director) {
                return $director;
            }
        }

        // Fallback: try to find any active director (excluding requester + assigned)
        $allDirectorRoles = ['direktur_hr', 'direktur_finance', 'direktur_it', 'direktur_sales', 'direktur_operasional'];
        return User::role($allDirectorRoles)
            ->where('is_active', true)
            ->whereNotIn('id', $allExcluded)
            ->first();
    }


    /**
     * Check if user can approve document
     */
    public function canApprove(Document $document, User $user): bool
    {
        return (int) $document->current_approver_id === (int) $user->id;
    }

    /**
     * Get approval progress percentage
     */
    public function getProgress(Document $document): int
    {
        $total = $document->approvals()->count();
        if ($total === 0) {
            return 0;
        }

        $completed = $document->approvals()
            ->whereIn('status', ['approved', 'rejected', 'skipped'])
            ->count();

        return (int) round(($completed / $total) * 100);
    }

    /**
     * Bulk approve multiple documents
     */
    public function bulkApprove(array $documentIds, User $approver, ?string $comment = null): array
    {
        $results = ['success' => 0, 'failed' => 0, 'errors' => []];

        foreach ($documentIds as $documentId) {
            try {
                $document = Document::find($documentId);
                if ($document && $this->canApprove($document, $approver)) {
                    $this->processApproval($document, $approver, 'approved', $comment);
                    $results['success']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = "Document #{$documentId}: Cannot approve";
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Document #{$documentId}: " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Get documents eligible for bulk approval
     */
    public function getBulkEligibleDocuments(User $approver): \Illuminate\Database\Eloquent\Collection
    {
        return Document::where('current_approver_id', $approver->id)
            ->whereIn('status', array_values($this->statusMapping))
            ->get();
    }
}
