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
        // Membatasi akses dashboard Pulse di lingkungan Production
        Gate::define('viewPulse', function (User $user) {
            return in_array($user->email, [
                'sukirno.k.semitro@gmail.com',
            ]);
        });
    }
}