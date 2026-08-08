<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display all notifications
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read and redirect to target URL.
     * Handles stale approval URLs by redirecting to documents.show instead.
     */
    public function markAsRead($notificationId)
    {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        $url = $notification->data['url'] ?? null;
        $documentId = $notification->data['document_id'] ?? null;

        if ($documentId) {
            $document = \App\Models\Document::find($documentId);
            
            // If the document is missing (deleted), clean up the notification and redirect back
            if (!$document) {
                $notification->delete();
                return redirect()->back()->with('error', 'Dokumen tidak ditemukan atau sudah dihapus.');
            }

            // If URL points to /approvals/{id}, redirect to /documents/{id} instead
            if ($url && preg_match('#/approvals/(\d+)#', $url, $matches)) {
                return redirect()->route('documents.show', $document);
            }
        }

        // Fallback: use the URL as-is, or go to dashboard
        return redirect($url ?? route('dashboard'));
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
