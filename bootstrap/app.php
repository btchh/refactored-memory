<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'prevent.back' => \App\Http\Middleware\PreventBackHistory::class,
            'isAdmin' => \App\Http\Middleware\IsAdmin::class,
            'isUser' => \App\Http\Middleware\IsUser::class,
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
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
