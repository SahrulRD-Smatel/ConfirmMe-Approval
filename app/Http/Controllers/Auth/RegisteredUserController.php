<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\NewUserRegisteredMail;
use App\Models\User;
use App\Notifications\NewUserRegisteredNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => false, // Requires admin approval
        ]);

        event(new Registered($user));

        // Notify all admin users about the new registration
        $this->notifyAdmins($user);

        // Do NOT auto-login — redirect to login with pending message
        return redirect()->route('login')->with('status',
            'Pendaftaran berhasil! Akun Anda sedang menunggu persetujuan administrator. Silakan hubungi admin untuk aktivasi dan penugasan role.'
        );
    }

    /**
     * Send in-app notification and email to all admin users.
     */
    private function notifyAdmins(User $newUser): void
    {
        $admins = User::role('admin')->where('is_active', true)->get();

        foreach ($admins as $admin) {
            // In-app notification (database)
            $admin->notify(new NewUserRegisteredNotification($newUser));

            // Email notification (wrapped in try-catch so mail failure
            // doesn't prevent registration from completing)
            if (config('app.env') !== 'testing') {
                try {
                    Mail::to($admin->email)->send(new NewUserRegisteredMail($newUser, $admin));
                } catch (\Exception $e) {
                    Log::warning('Failed to send new user registration email to admin', [
                        'admin_id' => $admin->id,
                        'admin_email' => $admin->email,
                        'new_user_id' => $newUser->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }
}
