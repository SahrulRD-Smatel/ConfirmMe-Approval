<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewUserRegisteredMail extends Mailable
{
    use SerializesModels;

    public User $newUser;
    public User $admin;

    /**
     * Email notification sent to admin users when a new user registers.
     */
    public function __construct(User $newUser, User $admin)
    {
        $this->newUser = $newUser;
        $this->admin = $admin;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[ConfirmMe] ' . __('User Baru Mendaftar') . ": {$this->newUser->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-user-registered',
            with: [
                'newUser' => $this->newUser,
                'admin' => $this->admin,
                'usersUrl' => route('admin.users.index'),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
