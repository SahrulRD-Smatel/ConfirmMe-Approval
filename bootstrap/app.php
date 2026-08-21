<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\UnauthorizedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'active' => \App\Http\Middleware\EnsureUserIsActive::class,
            'has_role' => \App\Http\Middleware\EnsureUserHasRole::class,
            'feature' => \App\Http\Middleware\FeatureToggleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Log authorization failures with detailed context for debugging on production
        $exceptions->renderable(function (AuthorizationException $e, $request) {
            $user = $request->user();
            Log::warning('Authorization failed (403)', [
                'user_id' => $user?->id,
                'user_name' => $user?->name,
                'user_email' => $user?->email,
                'user_roles' => $user?->getRoleNames()->toArray() ?? [],
                'user_permissions' => $user?->getAllPermissions()->pluck('name')->toArray() ?? [],
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'route' => $request->route()?->getName(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'message' => $e->getMessage(),
            ]);

            // Let Laravel handle the rendering (will use errors/403.blade.php)
            return null;
        });

        // Log Spatie UnauthorizedException (role middleware failures)
        $exceptions->renderable(function (UnauthorizedException $e, $request) {
            $user = $request->user();
            Log::warning('Spatie role/permission check failed (403)', [
                'user_id' => $user?->id,
                'user_name' => $user?->name,
                'user_roles' => $user?->getRoleNames()->toArray() ?? [],
                'required_roles' => $e->getRequiredRoles(),
                'required_permissions' => $e->getRequiredPermissions(),
                'url' => $request->fullUrl(),
                'route' => $request->route()?->getName(),
                'ip' => $request->ip(),
            ]);

            // Render as 403 using our custom error page
            abort(403, $e->getMessage());
        });
    })->create();

