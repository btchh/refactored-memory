<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['prefix' => 'api', 'middleware' => ['web', 'auth:web,admin']],
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global security headers for production
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        
        $middleware->alias([
            'prevent.back' => \App\Http\Middleware\PreventBackHistory::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'check.user.status' => \App\Http\Middleware\CheckUserStatus::class,
        ]);
        
        // Configure authentication redirects for different guards
        $middleware->redirectGuestsTo(function ($request) {
            // Check if the request is for admin routes
            if ($request->is('admin/*')) {
                return route('admin.login');
            }
            // Default to user login
            return route('user.login');
        });
        
        // Configure authenticated user redirects
        $middleware->redirectUsersTo(function ($request) {
            // Check if the request is for admin routes
            if ($request->is('admin/*')) {
                return route('admin.dashboard');
            }
            // Default to user dashboard
            return route('user.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Log all exceptions in production
        $exceptions->reportable(function (\Throwable $e) {
            // Add custom logging if needed
        });
        
        // Render custom error pages
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Not found'], 404);
            }
        });
        
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
        });
    })->create();
