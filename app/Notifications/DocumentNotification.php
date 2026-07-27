<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DocumentNotification extends Notification
{
    use Queueable;

    protected string $type;
    protected Document $document;
    protected string $message;
    protected ?string $url;

    /**
     * Notification types:
     * - 'submitted'    : Notifikasi ke approver bahwa ada dokumen baru
     * - 'approved'     : Notifikasi ke requester bahwa dokumen disetujui
     * - 'rejected'     : Notifikasi ke requester bahwa dokumen ditolak
     * - 'next_step'    : Notifikasi ke approver berikutnya
     * - 'completed'    : Notifikasi ke requester bahwa dokumen selesai (fully approved)
     */
    public function __construct(string $type, Document $document, string $message, ?string $url = null)
    {
        $this->type = $type;
        $this->document = $document;
        $this->message = $message;
        $this->url = $url ?? route('documents.show', $document);
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => $this->type,
            'document_id' => $this->document->id,
            'document_number' => $this->document->document_number,
            'title' => $this->getTitle(),
            'message' => $this->message,
            'url' => $this->url,
            'icon' => $this->getIcon(),
        ];
    }

    protected function getTitle(): string
    {
        return match ($this->type) {
            'submitted' => 'Dokumen Baru Perlu Review',
            'approved' => 'Dokumen Disetujui',
            'rejected' => 'Dokumen Ditolak',
            'next_step' => 'Menunggu Review Anda',
            'completed' => 'Dokumen Selesai Disetujui',
            'revision_requested' => 'Dokumen Perlu Direvisi',
            'disbursement' => 'Dana Sudah Dicairkan',
            'amount_changed' => 'Nominal Pengajuan Diubah — Perlu Konfirmasi',
            default => 'Notifikasi Dokumen',
        };
    }

    protected function getIcon(): string
    {
        return match ($this->type) {
            'submitted' => 'document-text',
            'approved' => 'check-circle',
            'rejected' => 'x-circle',
            'next_step' => 'clock',
            'completed' => 'check-badge',
            'revision_requested' => 'pencil-square',
            'disbursement' => 'banknotes',
            'amount_changed' => 'exclamation-triangle',
            default => 'bell',
        };
    }
}
