<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Redirect authenticated admins to admin dashboard
                if ($guard === 'admin') {
                    return redirect()->route('admin.dashboard');
                }

                // Redirect authenticated web users to user dashboard
                if ($guard === 'web' || $guard === null) {
                    return redirect()->route('user.dashboard');
                }
            }
        }

        return $next($request);
    }
}
