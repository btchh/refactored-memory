<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if admin is authenticated with the 'admin' guard
        if (!Auth::guard('admin')->check()) {
            // Return JSON response for API/AJAX requests or admin routes
            if ($request->expectsJson() || $request->is('admin/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login as an admin to access this resource.',
                ], 401);
            }

            return redirect()->route('admin.login')->with('error', 'Please login as an admin to access this page.');
        }

        // Prevent regular user from accessing admin routes
        if (Auth::guard('web')->check()) {
            // Return JSON response for API/AJAX requests or admin routes
            if ($request->expectsJson() || $request->is('admin/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden. Users cannot access admin resources.',
                ], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Users cannot access admin pages.');
        }

        return $next($request);
    }
}
