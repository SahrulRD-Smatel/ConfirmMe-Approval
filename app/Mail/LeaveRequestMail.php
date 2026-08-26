<?php

namespace App\Mail;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeaveRequestMail extends Mailable
{
    use SerializesModels;

    public LeaveRequest $leaveRequest;
    public string $action;
    public User $recipient;
    public ?User $actor;

    /**
     * Create a new message instance.
     */
    public function __construct(
        LeaveRequest $leaveRequest,
        string $action,
        User $recipient,
        ?User $actor = null,
    ) {
        $this->leaveRequest = $leaveRequest;
        $this->action = $action;
        $this->recipient = $recipient;
        $this->actor = $actor;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->getSubject(),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.leave-request',
            with: [
                'leaveRequest' => $this->leaveRequest,
                'action' => $this->action,
                'recipient' => $this->recipient,
                'actor' => $this->actor,
                'actionColor' => $this->getActionColor(),
                'greeting' => $this->getGreeting(),
                'bodyContent' => $this->getBody(),
                'leaveUrl' => $this->getLeaveUrl(),
            ]
        );
    }

    /**
     * Get email subject based on action
     */
    protected function getSubject(): string
    {
        $requester = $this->leaveRequest->user;
        $type = $this->leaveRequest->type_label;

        return match ($this->action) {
            'submitted' => '[ConfirmMe] ' . __('Pengajuan Cuti') . ": {$requester->name} - {$type}",
            'tier1_approved' => '[ConfirmMe] ' . __('Cuti Disetujui Atasan - Menunggu Validasi HR'),
            'pending_hr' => '[ConfirmMe] ' . __('Validasi Cuti') . ": {$requester->name} - {$type}",
            'approved' => '[ConfirmMe] ' . __('Cuti Disetujui') . ": {$requester->name}",
            'rejected' => '[ConfirmMe] ' . __('Cuti Ditolak') . ": {$requester->name}",
            default => '[ConfirmMe] ' . __('Notifikasi Cuti'),
        };
    }

    /**
     * Get greeting text
     */
    protected function getGreeting(): string
    {
        return __('Yth.') . " {$this->recipient->name},";
    }

    /**
     * Get body content based on action
     */
    protected function getBody(): string
    {
        $requester = $this->leaveRequest->user;
        $type = $this->leaveRequest->type_label;
        $duration = $this->leaveRequest->duration;
        $actorName = $this->actor->name ?? 'Unknown';

        return match ($this->action) {
            'submitted' => '<strong>' . $requester->name . '</strong> ' . __('mengajukan') . ' <strong>' . $type . '</strong> ' . __('selama') . ' <strong>' . $duration . ' ' . __('hari') . '</strong>. ' . __('Mohon review dan berikan keputusan Anda.'),
            'tier1_approved' => __('Pengajuan') . " {$type} " . __('Anda telah') . ' <strong>' . __('disetujui') . '</strong> ' . __('oleh') . ' <strong>' . $actorName . '</strong>. ' . __('Saat ini menunggu validasi administratif dari HR.'),
            'pending_hr' => __('Pengajuan') . " {$type} " . __('dari') . ' <strong>' . $requester->name . '</strong> ' . __('telah disetujui atasan') . ' (<strong>' . $actorName . '</strong>). ' . __('Mohon lakukan validasi administratif.'),
            'approved' => __('Pengajuan') . " {$type} " . __('Anda telah') . ' <strong>' . __('disetujui sepenuhnya') . '</strong> ' . __('oleh') . ' <strong>' . $actorName . '</strong>. ' . __('Selamat menikmati waktu istirahat Anda!'),
            'rejected' => __('Mohon maaf, pengajuan') . " {$type} " . __('Anda telah') . ' <strong>' . __('ditolak') . '</strong> ' . __('oleh') . ' <strong>' . $actorName . '</strong>.',
            default => __('Ada update terkait pengajuan cuti.'),
        };
    }

    /**
     * Get action color for email styling
     */
    protected function getActionColor(): string
    {
        return match ($this->action) {
            'submitted' => '#F59E0B',   // amber
            'tier1_approved' => '#3B82F6', // blue
            'pending_hr' => '#F59E0B',  // amber
            'approved' => '#10B981',    // green
            'rejected' => '#EF4444',    // red
            default => '#6B7280',       // gray
        };
    }

    /**
     * Get leave URL
     */
    protected function getLeaveUrl(): string
    {
        if (in_array($this->action, ['submitted', 'pending_hr'])) {
            return route('leave-approvals.index');
        }
        return route('leave-requests.index');
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
