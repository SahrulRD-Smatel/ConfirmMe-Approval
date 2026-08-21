<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FeatureToggleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $featureKey): Response
    {
        if (!feature_active($featureKey)) {
            // For AJAX requests, return JSON error
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Fitur ini sedang dinonaktifkan oleh Administrator.'], 403);
            }
            
            // For normal requests, abort with 403 status and message
            abort(403, 'Fitur ini sedang dinonaktifkan oleh Administrator.');
        }

        return $next($request);
    }
}
