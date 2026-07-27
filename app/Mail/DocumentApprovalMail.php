<?php

namespace App\Mail;

use App\Models\Document;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentApprovalMail extends Mailable
{
    use SerializesModels;

    public Document $document;
    public string $action;
    public User $recipient;
    public ?User $actor;
    public string $templateId;
    public string $emailSubject;
    public string $greeting;
    public string $bodyContent;

    /**
     * Create a new message instance.
     */
    public function __construct(
        Document $document,
        string $action,
        User $recipient,
        ?User $actor = null,
        string $templateId = 'EML-00',
        string $emailSubject = '',
        string $greeting = '',
        string $bodyContent = ''
    ) {
        $this->document = $document;
        $this->action = $action;
        $this->recipient = $recipient;
        $this->actor = $actor;
        $this->templateId = $templateId;
        $this->emailSubject = $emailSubject ?: $this->getDefaultSubject();
        $this->greeting = $greeting ?: $this->getDefaultGreeting();
        $this->bodyContent = $bodyContent ?: $this->getDefaultBody();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.document-approval',
            with: [
                'document' => $this->document,
                'action' => $this->action,
                'recipient' => $this->recipient,
                'actor' => $this->actor,
                'templateId' => $this->templateId,
                'subject' => $this->emailSubject,
                'greeting' => $this->greeting,
                'bodyContent' => $this->bodyContent,
                'actionColor' => $this->getActionColor(),
                'approvalUrl' => $this->getApprovalUrl(),
                'documentUrl' => route('documents.show', $this->document),
                'previousApprovers' => $this->getPreviousApprovers(),
            ]
        );
    }

    /**
     * Get default subject based on action
     */
    protected function getDefaultSubject(): string
    {
        return match ($this->action) {
            'pending_approval' => "[ConfirmMe] Mohon Review: {$this->document->title}",
            'approved' => "[ConfirmMe] Dokumen Disetujui: {$this->document->document_number}",
            'rejected' => "[ConfirmMe] Dokumen Ditolak: {$this->document->document_number}",
            'completed' => "[ConfirmMe] Dokumen Selesai: {$this->document->document_number}",
            default => "[ConfirmMe] Notifikasi: {$this->document->document_number}",
        };
    }

    /**
     * Get default greeting
     */
    protected function getDefaultGreeting(): string
    {
        return "Yth. {$this->recipient->name},";
    }

    /**
     * Get default body
     */
    protected function getDefaultBody(): string
    {
        return match ($this->action) {
            'pending_approval' => "Ada dokumen **{$this->document->document_number}** yang memerlukan persetujuan Anda.",
            'approved' => "Dokumen Anda **{$this->document->document_number}** telah disetujui.",
            'rejected' => "Dokumen Anda **{$this->document->document_number}** telah ditolak.",
            'completed' => "Dokumen Anda **{$this->document->document_number}** telah selesai diproses.",
            default => "Ada update untuk dokumen **{$this->document->document_number}**.",
        };
    }

    /**
     * Get action color for email styling
     */
    protected function getActionColor(): string
    {
        return match ($this->action) {
            'pending_approval' => '#F59E0B', // amber
            'approved' => '#10B981', // green
            'rejected' => '#EF4444', // red
            'completed' => '#3B82F6', // blue
            default => '#6B7280', // gray
        };
    }

    /**
     * Get approval URL (only for pending_approval action)
     */
    protected function getApprovalUrl(): ?string
    {
        if ($this->action === 'pending_approval') {
            return route('approvals.show', $this->document);
        }
        return route('documents.show', $this->document);
    }

    /**
     * Get list of previous approvers for display in email
     */
    protected function getPreviousApprovers(): array
    {
        return $this->document->approvals()
            ->where('status', 'approved')
            ->with('approver')
            ->orderBy('approval_order')
            ->get()
            ->map(fn($approval) => [
                'name' => $approval->approver->name ?? 'Unknown',
                'role' => config("workflow.role_labels.{$approval->approver_role}", $approval->approver_role),
                'approved_at' => $approval->approved_at?->format('d M Y H:i'),
            ])
            ->toArray();
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
