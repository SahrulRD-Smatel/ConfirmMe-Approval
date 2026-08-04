<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Ensure the authenticated user has at least one role assigned.
     *
     * Users who registered but haven't been assigned a role by an admin
     * are redirected to a "pending setup" page. They can still access
     * their profile and logout.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->roles->isEmpty()) {
            // Allow access to profile, logout, and the pending page itself
            $allowedRoutes = [
                'profile.edit',
                'profile.update',
                'logout',
                'account.pending',
            ];

            if (in_array($request->route()?->getName(), $allowedRoutes)) {
                return $next($request);
            }

            return redirect()->route('account.pending');
        }

        return $next($request);
    }
}
