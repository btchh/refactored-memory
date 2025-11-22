<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // First, check if an admin is trying to access user routes (cross-guard check)
        if (Auth::guard('admin')->check()) {
            // Return JSON response for API/AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden. Admins cannot access user resources.',
                ], 403);
            }

            return redirect()->route('admin.dashboard')->with('error', 'Admins cannot access user pages.');
        }

        // Then check if user is authenticated with the 'web' guard
        if (!Auth::guard('web')->check()) {
            // Return JSON response for API/AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login as a user to access this resource.',
                ], 401);
            }

            return redirect()->route('user.login')->with('error', 'Please login as a user to access this page.');
        }

        return $next($request);
    }
}
