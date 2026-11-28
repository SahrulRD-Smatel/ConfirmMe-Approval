<?php

namespace App\Providers;

use App\Models\User; // <-- Ditambahkan agar model User terbaca
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; // <-- Digunakan untuk authorization Gate

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Carbon\Carbon::setLocale(config('app.locale', 'id'));

        // Super-admin / admin role bypass for permissions and general gates,
        // but preserve policy-specific lifecycle checks (update, delete, submit, approve, reject, print)
        // so that completed/approved/paid documents remain strictly immutable.
        Gate::before(function ($user, $ability, $arguments = []) {
            if (in_array($ability, ['update', 'delete', 'submit', 'approve', 'reject', 'print'])) {
                return null;
            }
            return $user->hasRole('admin') ? true : null;
        });

        // Membatasi akses dashboard Pulse di lingkungan Production
        Gate::define('viewPulse', function (User $user) {
            return in_array($user->email, [
                'sukirno.k.semitro@gmail.com',
            ]);
        });
    }
}