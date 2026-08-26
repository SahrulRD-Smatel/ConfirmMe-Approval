<?php

namespace App\Services;

use App\Models\Document;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Mail\DocumentApprovalMail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleSwitchable;

/**
 * Email Notification Service
 * 
 * Handles dynamic TO/CC recipient calculation based on approval workflow.
 * Templates are loaded from database for easy production changes.
 * 
 * Berdasarkan Konfigurasi Email PT INFO SOLUSINDO DATA UTAMA:
 * - TO: Next approver (siapa yang harus approve selanjutnya)
 * - CC: Requester + all previous approvers who already approved
 * - Special CC: When at presiden_direktur stage, add Finance Director + Division Director
 */
class EmailNotificationService
{
    use LocaleSwitchable;

    /**
     * Cache TTL for email templates (in seconds)
     */
    protected int $cacheTtl = 3600; // 1 hour

    /**
     * Get the notification configuration for a document
     */
    public function getNotificationConfig(Document $document): array
    {
        $nextApprover = $this->getNextApprover($document);
        $ccRecipients = $this->getCCRecipients($document);

        // Add special CC recipients for final stage (presiden_direktur)
        if ($nextApprover && $this->isPresidenDirektStage($document)) {
            $specialCC = $this->getFinalStageCCRecipients($document);
            $ccRecipients = $ccRecipients->merge($specialCC)->unique('id');
        }

        return [
            'to' => $nextApprover,
            'cc' => $ccRecipients,
        ];
    }

    /**
     * Get the next approver (TO recipient)
     */
    public function getNextApprover(Document $document): ?User
    {
        if ($document->current_approver_id) {
            return User::find($document->current_approver_id);
        }

        $pendingApproval = $document->approvals()
            ->where('status', 'pending')
            ->orderBy('approval_order')
            ->first();

        return $pendingApproval ? $pendingApproval->approver : null;
    }

    /**
     * Get CC recipients: Requester + all previous approvers who approved
     */
    public function getCCRecipients(Document $document): Collection
    {
        $ccList = collect();

        // 1. Always include the requester (pemohon)
        $requester = $document->user;
        if ($requester) {
            $ccList->push($requester);
        }

        // 2. Get all previous approvers who already approved
        $previousApprovers = $document->approvals()
            ->where('status', 'approved')
            ->with('approver')
            ->get()
            ->pluck('approver')
            ->filter();

        $ccList = $ccList->merge($previousApprovers);

        return $ccList->unique('id');
    }

    /**
     * Get special CC recipients for final stage (when going to Presiden Direktur)
     */
    public function getFinalStageCCRecipients(Document $document): Collection
    {
        $specialCC = collect();

        // 1. Add Finance Director
        $financeDirector = User::role('direktur_finance')
            ->where('is_active', true)
            ->first();
        if ($financeDirector) {
            $specialCC->push($financeDirector);
        }

        // 2. Add Division Director based on document's department
        $divisionDirector = $this->getDivisionDirector($document);
        if ($divisionDirector) {
            $specialCC->push($divisionDirector);
        }

        return $specialCC->unique('id');
    }

    /**
     * Check if document is at presiden_direktur stage
     */
    protected function isPresidenDirektStage(Document $document): bool
    {
        $nextApprover = $this->getNextApprover($document);
        if (!$nextApprover) {
            return false;
        }
        return $nextApprover->hasRole('presiden_direktur');
    }

    /**
     * Get the division director for a document based on department
     */
    protected function getDivisionDirector(Document $document): ?User
    {
        $department = $document->department;

        $directorRole = \App\Models\Department::directorRoleFor($department);

        if ($directorRole) {
            return User::role($directorRole)
                ->where('is_active', true)
                ->first();
        }

        return null;
    }

    /**
     * Get the last approver (previous approver who approved)
     */
    protected function getLastApprover(Document $document): ?User
    {
        $lastApproval = $document->approvals()
            ->where('status', 'approved')
            ->orderBy('approval_order', 'desc')
            ->first();

        return $lastApproval ? $lastApproval->approver : null;
    }

    /**
     * Get email template from database
     * Falls back to config if not found
     */
    protected function getEmailTemplate(Document $document, string $action): ?EmailTemplate
    {
        $approvalType = $document->approval_type;
        $isPresidenStage = $this->isPresidenDirektStage($document);

        // Determine target based on action and stage
        $target = match (true) {
            $action === 'rejected' => 'rejected',
            $action === 'completed' => 'completed',
            $action === 'pending_approval' && $isPresidenStage => 'presiden',
            $action === 'pending_approval' && $this->isFinanceStage($document) => 'finance',
            $action === 'pending_approval' && $this->isHRorFinanceStage($document) => 'hr_finance',
            default => 'next_approver',
        };

        // Try to find template from database with caching
        $cacheKey = "email_template_{$approvalType}_{$target}";

        $template = Cache::remember($cacheKey, $this->cacheTtl, function () use ($approvalType, $target) {
            // First try exact workflow match
            $template = EmailTemplate::where('workflow', $approvalType)
                ->where('target', $target)
                ->where('is_active', true)
                ->first();

            // If not found, try 'all' workflow (generic templates)
            if (!$template) {
                $template = EmailTemplate::where('workflow', 'all')
                    ->where('target', $target)
                    ->where('is_active', true)
                    ->first();
            }

            return $template;
        });

        return $template;
    }

    /**
     * Get fallback template if database template not found
     */
    protected function getFallbackTemplate(string $action): array
    {
        return match ($action) {
            'pending_approval' => [
                'id' => 'EML-00',
                'subject' => '[ConfirmMe] ' . __('Mohon Review') . ': {{doc_subject}}',
                'greeting' => __('Yth.') . ' {{next_approver_name}},',
                'body' => __('Ada dokumen') . ' **{{doc_id}}** ' . __('yang memerlukan persetujuan Anda') . '.',
            ],
            'rejected' => [
                'id' => 'EML-08',
                'subject' => '[ConfirmMe] ' . __('Dokumen DITOLAK') . ': {{doc_id}}',
                'greeting' => __('Halo') . ' {{staff_name}},',
                'body' => __('Dokumen Anda') . ' **{{doc_id}}** ' . __('telah ditolak') . '. ' . __('Alasan') . ': {{reason}}.',
            ],
            'completed' => [
                'id' => 'EML-10',
                'subject' => '[ConfirmMe] ' . __('Dokumen') . ' {{doc_id}} - APPROVED',
                'greeting' => __('Selamat') . ' {{staff_name}},',
                'body' => __('Dokumen') . ' **{{doc_id}}** ' . __('telah selesai diproses dan disetujui') . '.',
            ],
            default => [
                'id' => 'EML-00',
                'subject' => '[ConfirmMe] ' . __('Notifikasi') . ': {{doc_id}}',
                'greeting' => __('Halo') . ',',
                'body' => __('Ada update untuk dokumen') . ' **{{doc_id}}**.',
            ],
        };
    }

    /**
     * Check if current stage is Finance
     */
    protected function isFinanceStage(Document $document): bool
    {
        $nextApprover = $this->getNextApprover($document);
        return $nextApprover && $nextApprover->hasRole('finance');
    }

    /**
     * Check if current stage is HR or Finance
     */
    protected function isHRorFinanceStage(Document $document): bool
    {
        $nextApprover = $this->getNextApprover($document);
        return $nextApprover && ($nextApprover->hasRole('hrd') || $nextApprover->hasRole('finance'));
    }

    /**
     * Build placeholders array from document
     */
    protected function buildPlaceholders(Document $document, ?User $actor = null): array
    {
        $nextApprover = $this->getNextApprover($document);
        $lastApprover = $this->getLastApprover($document);
        $metadata = $document->metadata ?? [];

        // Get the Finance-approved amount (if any)
        $financeApproval = $document->approvals()
            ->where('status', 'approved')
            ->where('approver_role', 'finance')
            ->whereNotNull('approved_amount')
            ->first();

        $estimatedBudget = $document->estimated_budget ?? 0;
        $approvedAmount = $financeApproval?->approved_amount;
        $displayAmount = $approvedAmount ?? $estimatedBudget;
        $budgetChanged = $approvedAmount && (float) $approvedAmount !== (float) $estimatedBudget;

        return [
            'doc_id' => $document->document_number,
            'doc_subject' => $document->title,
            'staff_name' => $document->user->name ?? 'Unknown',
            'staff_email' => $document->user->email ?? '',
            'next_approver_name' => $nextApprover->name ?? 'Approver',
            'next_approver_email' => $nextApprover->email ?? '',
            'prev_approver_name' => $lastApprover->name ?? 'Previous Approver',
            'amount' => 'Rp ' . number_format($displayAmount, 0, ',', '.'),
            'original_amount' => 'Rp ' . number_format($estimatedBudget, 0, ',', '.'),
            'approved_amount' => $approvedAmount ? 'Rp ' . number_format($approvedAmount, 0, ',', '.') : null,
            'budget_changed' => $budgetChanged,
            'vendor_name' => $metadata['vendor_name'] ?? '-',
            'rejected_by_name' => $actor->name ?? 'Approver',
            'reason' => $document->rejection_reason ?? '-',
            'company_name' => config('email_templates.company.name', 'PT INFO SOLUSINDO DATA UTAMA'),
            'app_url' => config('app.url', 'http://localhost'),
        ];
    }

    /**
     * Send approval notification email
     */
    public function sendApprovalNotification(Document $document, string $action, ?User $actor = null): void
    {
        if (!config('workflow.email_notifications.enabled', true)) {
            Log::info("Email notifications disabled, skipping for document {$document->document_number}");
            return;
        }

        try {
            // Determine the primary recipient (TO) to set locale
            $primaryRecipient = match ($action) {
                'pending_approval' => $this->getNextApprover($document),
                'rejected', 'completed' => $document->user,
                default => $document->user,
            };

            if (!$primaryRecipient) {
                Log::warning("No primary recipient found for document {$document->document_number}, action: {$action}");
                return;
            }

            // Switch locale to recipient's language, render and send, then restore
            $this->withRecipientLocale($primaryRecipient, function () use ($document, $action, $actor) {
                $recipientLocale = app()->getLocale();

                // Get template from database
                $template = $this->getEmailTemplate($document, $action);
                $placeholders = $this->buildPlaceholders($document, $actor);

                if ($template) {
                    $templateId = $template->code;
                    $subject = $template->getProcessedSubject($placeholders, $recipientLocale);
                    $greeting = $template->getProcessedGreeting($placeholders, $recipientLocale);
                    $bodyContent = $template->getProcessedBody($placeholders, $recipientLocale);
                } else {
                    // Use fallback if no database template found
                    // Note: __() calls inside getFallbackTemplate() will use the switched locale
                    $fallback = $this->getFallbackTemplate($action);
                    $templateId = $fallback['id'];
                    $subject = $this->replacePlaceholders($fallback['subject'], $placeholders);
                    $greeting = $this->replacePlaceholders($fallback['greeting'], $placeholders);
                    $bodyContent = $this->replacePlaceholders($fallback['body'], $placeholders);

                    Log::warning("No database template found for {$document->approval_type}/{$action}, using fallback");
                }

                switch ($action) {
                    case 'pending_approval':
                        $this->sendPendingApprovalEmail($document, $templateId, $subject, $greeting, $bodyContent);
                        break;

                    case 'approved':
                        $this->sendApprovedEmail($document, $actor, $templateId, $subject, $greeting, $bodyContent);
                        break;

                    case 'rejected':
                        $this->sendRejectedEmail($document, $actor, $templateId, $subject, $greeting, $bodyContent);
                        break;

                    case 'completed':
                        $this->sendCompletedEmail($document, $templateId, $subject, $greeting, $bodyContent);
                        break;
                }
            });
        } catch (\Throwable $e) {
            Log::error("Failed to send email notification for document {$document->document_number}: " . $e->getMessage(), [
                'exception' => get_class($e),
                'action' => $action,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Replace placeholders in text (for fallback)
     */
    protected function replacePlaceholders(string $text, array $placeholders): string
    {
        foreach ($placeholders as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value ?? '', $text);
        }
        return $text;
    }

    /**
     * Send email to next approver (pending approval)
     */
    protected function sendPendingApprovalEmail(
        Document $document,
        string $templateId,
        string $subject,
        string $greeting,
        string $bodyContent
    ): void {
        $config = $this->getNotificationConfig($document);

        if (!$config['to']) {
            Log::warning("No next approver found for document {$document->document_number}");
            return;
        }

        $mail = new DocumentApprovalMail(
            $document,
            'pending_approval',
            $config['to'],
            null,
            $templateId,
            $subject,
            $greeting,
            $bodyContent
        );

        $mailBuilder = Mail::to($config['to']);

        if ($config['cc']->isNotEmpty()) {
            $ccEmails = $config['cc']
                ->filter(fn($user) => $user->id !== $config['to']->id)
                ->pluck('email')
                ->toArray();

            if (!empty($ccEmails)) {
                $mailBuilder->cc($ccEmails);
            }
        }

        if (config('workflow.email_notifications.queue', false)) {
            $mailBuilder->queue($mail);
        } else {
            $mailBuilder->send($mail);
        }

        Log::info("Sent pending approval email [{$templateId}] for document {$document->document_number} to {$config['to']->email}");
    }

    /**
     * Send notification that document was approved
     */
    protected function sendApprovedEmail(
        Document $document,
        ?User $actor,
        string $templateId,
        string $subject,
        string $greeting,
        string $bodyContent
    ): void {
        $requester = $document->user;
        if (!$requester)
            return;

        $mail = new DocumentApprovalMail(
            $document,
            'approved',
            $requester,
            $actor,
            $templateId,
            $subject,
            $greeting,
            $bodyContent
        );

        if (config('workflow.email_notifications.queue', false)) {
            Mail::to($requester)->queue($mail);
        } else {
            Mail::to($requester)->send($mail);
        }

        Log::info("Sent approval notification [{$templateId}] for document {$document->document_number} to {$requester->email}");
    }

    /**
     * Send notification that document was rejected
     */
    protected function sendRejectedEmail(
        Document $document,
        ?User $actor,
        string $templateId,
        string $subject,
        string $greeting,
        string $bodyContent
    ): void {
        $requester = $document->user;
        if (!$requester)
            return;

        $mail = new DocumentApprovalMail(
            $document,
            'rejected',
            $requester,
            $actor,
            $templateId,
            $subject,
            $greeting,
            $bodyContent
        );

        $ccRecipients = $this->getCCRecipients($document)
            ->filter(fn($user) => $user->id !== $requester->id);

        $mailBuilder = Mail::to($requester);

        if ($ccRecipients->isNotEmpty()) {
            $mailBuilder->cc($ccRecipients->pluck('email')->toArray());
        }

        if (config('workflow.email_notifications.queue', false)) {
            $mailBuilder->queue($mail);
        } else {
            $mailBuilder->send($mail);
        }

        Log::info("Sent rejection notification [{$templateId}] for document {$document->document_number} to {$requester->email}");
    }

    /**
     * Send notification that document is fully approved/completed
     */
    protected function sendCompletedEmail(
        Document $document,
        string $templateId,
        string $subject,
        string $greeting,
        string $bodyContent
    ): void {
        $requester = $document->user;
        if (!$requester)
            return;

        $mail = new DocumentApprovalMail(
            $document,
            'completed',
            $requester,
            null,
            $templateId,
            $subject,
            $greeting,
            $bodyContent
        );

        $ccRecipients = $this->getCCRecipients($document)
            ->filter(fn($user) => $user->id !== $requester->id);

        $mailBuilder = Mail::to($requester);

        if ($ccRecipients->isNotEmpty()) {
            $mailBuilder->cc($ccRecipients->pluck('email')->toArray());
        }

        if (config('workflow.email_notifications.queue', false)) {
            $mailBuilder->queue($mail);
        } else {
            $mailBuilder->send($mail);
        }

        Log::info("Sent completion notification [{$templateId}] for document {$document->document_number} to {$requester->email}");
    }

    /**
     * Clear email template cache
     * Call this when templates are updated in database
     */
    public function clearTemplateCache(): void
    {
        $workflows = ['only', 'po', 'invoice', 'loan', 'all'];
        $targets = ['next_approver', 'presiden', 'finance', 'hr_finance', 'completed', 'rejected'];

        foreach ($workflows as $workflow) {
            foreach ($targets as $target) {
                Cache::forget("email_template_{$workflow}_{$target}");
            }
        }

        Log::info('Email template cache cleared');
    }
}
