<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            
            // Check if user is archived (soft deleted)
            if ($user->trashed()) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('user.login')->with([
                    'error' => 'Your account has been suspended. You cannot access the site at this time. Please contact support for assistance.',
                    'account_restricted' => true
                ]);
            }
            
            // Check if user is disabled
            if ($user->status === 'disabled') {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('user.login')->with([
                    'error' => 'Your account has been temporarily disabled. Please contact support to reactivate your account.',
                    'account_restricted' => true
                ]);
            }
        }
        
        return $next($request);
    }
}
