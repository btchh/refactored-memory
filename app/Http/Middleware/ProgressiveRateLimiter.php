<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ProgressiveRateLimiter
{
    /**
     * Handle an incoming request with progressive rate limiting.
     * Each failed attempt increases lockout time by 5 minutes.
     */
    public function handle(Request $request, Closure $next, string $limiterName = 'default'): Response
    {
        $key = $this->resolveRequestSignature($request, $limiterName);
        $attemptsKey = $key . ':attempts';
        $lockoutKey = $key . ':lockout';
        
        // Check if currently locked out
        if (Cache::has($lockoutKey)) {
            $remainingSeconds = Cache::get($lockoutKey) - now()->timestamp;
            $remainingMinutes = ceil($remainingSeconds / 60);
            
            return response()->json([
                'message' => "Too many attempts. Please try again in {$remainingMinutes} minute(s).",
                'retry_after' => $remainingSeconds,
            ], 429);
        }
        
        // Get current attempt count
        $attempts = Cache::get($attemptsKey, 0);
        
        // Define limits based on action type
        $maxAttempts = $this->getMaxAttempts($limiterName);
        
        // Check if exceeded attempts
        if ($attempts >= $maxAttempts) {
            // Calculate progressive lockout time (5 minutes per violation)
            $violations = Cache::get($key . ':violations', 0) + 1;
            $lockoutMinutes = $violations * 5;
            $lockoutSeconds = $lockoutMinutes * 60;
            
            // Store violation count and lockout time
            Cache::put($key . ':violations', $violations, now()->addHours(24));
            Cache::put($lockoutKey, now()->timestamp + $lockoutSeconds, now()->addSeconds($lockoutSeconds));
            
            // Reset attempts counter
            Cache::forget($attemptsKey);
            
            return response()->json([
                'message' => "Too many attempts. Account locked for {$lockoutMinutes} minute(s).",
                'retry_after' => $lockoutSeconds,
                'lockout_minutes' => $lockoutMinutes,
            ], 429);
        }
        
        // Increment attempt counter
        Cache::put($attemptsKey, $attempts + 1, now()->addMinutes(15));
        
        $response = $next($request);
        
        // Reset attempts on successful request (2xx status)
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            Cache::forget($attemptsKey);
            // Optionally reset violations after successful action
            // Cache::forget($key . ':violations');
        }
        
        return $response;
    }
    
    /**
     * Resolve the request signature for rate limiting.
     */
    protected function resolveRequestSignature(Request $request, string $limiterName): string
    {
        // Use IP + limiter name for anonymous requests
        if (!$request->user() && !$request->user('admin')) {
            return 'rate_limit:' . $limiterName . ':' . $request->ip();
        }
        
        // Use user ID + limiter name for authenticated requests
        $userId = $request->user()?->id ?? $request->user('admin')?->id ?? 'guest';
        $guard = $request->user() ? 'user' : 'admin';
        
        return 'rate_limit:' . $limiterName . ':' . $guard . ':' . $userId;
    }
    
    /**
     * Get max attempts based on limiter type.
     */
    protected function getMaxAttempts(string $limiterName): int
    {
        return match($limiterName) {
            'login' => 5,           // 5 login attempts
            'register' => 3,        // 3 registration attempts
            'otp' => 3,             // 3 OTP requests
            'booking' => 10,        // 10 booking attempts
            default => 5,
        };
    }
}
