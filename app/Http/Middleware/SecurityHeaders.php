<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Security headers for production environment.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only add security headers in production
        if (app()->environment('production')) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            $response->headers->set('Permissions-Policy', 'geolocation=(self), microphone=(), camera=()');
            
            // Remove server header if possible
            $response->headers->remove('X-Powered-By');
        }

        return $response;
    }
}
