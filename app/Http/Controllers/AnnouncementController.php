<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\User;
use App\Notifications\AnnouncementNotification;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Centralized authorization check for announcement management.
     * Only HRD, Direktur HR, Admin, Presiden Direktur, or HR Department users can manage.
     */
    private function authorizeAnnouncementAccess(): void
    {
        $user = auth()->user();
        $allowedRoles = ['hrd', 'direktur_hr', 'admin', 'presiden_direktur'];

        if (!$user->hasAnyRole($allowedRoles) && $user->department !== 'Human Resources') {
            abort(403, 'Anda tidak memiliki akses untuk mengelola pengumuman.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorizeAnnouncementAccess();

        $announcements = Announcement::with('creator')
            ->latest()
            ->paginate(10);

        return view('announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorizeAnnouncementAccess();

        return view('announcements.create');
    }

    /**
     * Display the specified announcement.
     * Redirects to index since announcements are displayed in the dashboard feed.
     */
    public function show(Announcement $announcement)
    {
        $this->authorizeAnnouncementAccess();

        return redirect()->route('announcements.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeAnnouncementAccess();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:info,warning,urgent,danger',
            'attachments.*' => 'nullable|file|max:10240', // Max 10MB per file
        ]);

        $announcement = Announcement::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => $validated['type'],
            'created_by' => auth()->id(),
            'is_active' => true,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('announcements', 'public');
                $ext = strtolower($file->getClientOriginalExtension());

                $type = 'document';
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $type = 'image';
                } elseif (in_array($ext, ['mp4', 'mov', 'avi', 'mpeg', 'webm'])) {
                    $type = 'video';
                }

                $announcement->attachments()->create([
                    'file_path' => $path,
                    'file_type' => $type,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }
        // Broadcast in-app notification to all active users (bell icon)
        try {
            $activeUsers = User::where('is_active', true)
                ->where('id', '!=', auth()->id())
                ->get();

            foreach ($activeUsers as $activeUser) {
                $activeUser->notify(new AnnouncementNotification(
                    'Pengumuman Baru: ' . $announcement->title,
                    \Illuminate\Support\Str::limit(strip_tags($announcement->content), 100),
                    route('dashboard') // or route to announcement detail
                ));
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to send announcement notifications: ' . $e->getMessage());
        }

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman berhasil dibuat dan notifikasi terkirim ke semua karyawan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement)
    {
        $this->authorizeAnnouncementAccess();

        return view('announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $this->authorizeAnnouncementAccess();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:info,warning,urgent,danger',
            'is_active' => 'boolean',
            'attachments.*' => 'nullable|file|max:10240',
            'delete_attachments' => 'nullable|array',
            'delete_attachments.*' => 'exists:announcement_attachments,id',
        ]);

        $announcement->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => $validated['type'],
            'is_active' => $validated['is_active'],
        ]);

        // Handle new attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('announcements', 'public');
                $ext = strtolower($file->getClientOriginalExtension());

                $type = 'document';
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $type = 'image';
                } elseif (in_array($ext, ['mp4', 'mov', 'avi', 'mpeg', 'webm'])) {
                    $type = 'video';
                }

                $announcement->attachments()->create([
                    'file_path' => $path,
                    'file_type' => $type,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        // Handle attachment deletion
        if ($request->has('delete_attachments')) {
            $attachmentsToDelete = \App\Models\AnnouncementAttachment::whereIn('id', $request->delete_attachments)
                ->where('announcement_id', $announcement->id)
                ->get();

            foreach ($attachmentsToDelete as $attachment) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }
        }

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        $this->authorizeAnnouncementAccess();

        foreach ($announcement->attachments as $attachment) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($attachment->file_path);
        }

        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus!');
    }
}
