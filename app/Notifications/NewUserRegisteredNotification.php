<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewUserRegisteredNotification extends Notification
{
    use Queueable;

    protected User $newUser;

    /**
     * In-app notification sent to admins when a new user registers.
     * Appears in the bell icon dropdown and notifications page.
     */
    public function __construct(User $newUser)
    {
        $this->newUser = $newUser;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'new_user',
            'title' => 'User Baru Mendaftar',
            'message' => "{$this->newUser->name} ({$this->newUser->email}) baru saja mendaftar dan menunggu aktivasi serta penugasan role.",
            'url' => route('admin.users.index'),
            'action_url' => route('admin.users.index'),
            'icon' => 'user-plus',
            'new_user_id' => $this->newUser->id,
            'new_user_name' => $this->newUser->name,
            'new_user_email' => $this->newUser->email,
        ];
    }
}
