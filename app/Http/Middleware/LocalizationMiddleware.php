<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // When language switcher feature is disabled, lock entire application to Indonesian
        if (!feature_active('feature_language_switch')) {
            App::setLocale('id');
            return $next($request);
        }

        if (Auth::check()) {
            $locale = Auth::user()->locale ?? 'id';
            App::setLocale($locale);
        } else if ($request->session()->has('locale')) {
            App::setLocale($request->session()->get('locale'));
        } else {
            App::setLocale('id'); // Default
        }

        return $next($request);
    }
}
