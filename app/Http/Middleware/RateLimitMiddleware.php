<?php

namespace App\Http\Middleware;

use App\Services\RateLimitService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    public function __construct(
        protected RateLimitService $rateLimitService
    ) {}

    /**
     * Handle an incoming request with database-backed rate limiting.
     * Shows warnings instead of hard blocks for normal users.
     * Only blocks for obvious spam attacks.
     */
    public function handle(Request $request, Closure $next, string $actionType = 'default'): Response
    {
        $identifier = $this->resolveIdentifier($request, $actionType);
        $ipAddress = $request->ip();
        
        // Check current state before processing
        $state = $this->rateLimitService->checkState($identifier, $ipAddress, $actionType);
        
        // Only block for obvious spam attacks
        if ($state['action'] === 'block') {
            return $this->blockResponse($request, $state['message']);
        }
        
        // Process the request
        $response = $next($request);
        
        // On failed request (non-2xx), record the attempt
        if ($response->getStatusCode() >= 400) {
            $newState = $this->rateLimitService->recordFailedAttempt($identifier, $ipAddress, $actionType);
            
            // Add warning to session if applicable
            if ($newState['action'] === 'warning' && !$request->expectsJson()) {
                session()->flash('warning', $newState['message']);
            }
            
            // Handle redirect for too many attempts (but not spam)
            if ($newState['action'] === 'redirect') {
                return $this->redirectResponse($request, $actionType, $newState['message']);
            }
        }
        
        // On success, clear attempts
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $this->rateLimitService->clearAttempts($identifier, $ipAddress, $actionType);
        }
        
        return $response;
    }

    /**
     * Resolve identifier based on action type and request.
     */
    protected function resolveIdentifier(Request $request, string $actionType): string
    {
        // For authenticated users, use their ID
        if ($user = $request->user() ?? $request->user('admin')) {
            return 'user:' . $user->id;
        }
        
        // For login/register, use the username/email from request
        if (in_array($actionType, ['login', 'register', 'otp'])) {
            return $request->input('username') 
                ?? $request->input('email') 
                ?? $request->input('identifier')
                ?? 'ip:' . $request->ip();
        }
        
        // Default to IP-based
        return 'ip:' . $request->ip();
    }

    /**
     * Return block response for spam attacks.
     */
    protected function blockResponse(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'blocked' => true,
            ], 429);
        }
        
        return redirect()->back()->with('error', $message);
    }

    /**
     * Return redirect response for too many attempts.
     */
    protected function redirectResponse(Request $request, string $actionType, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'action' => 'redirect',
            ], 429);
        }
        
        // Determine redirect route based on action type
        $route = match ($actionType) {
            'login' => $this->getLoginRedirectRoute($request),
            'otp', 'register' => route('user.register'),
            default => url()->previous(),
        };
        
        return redirect($route)->with('warning', $message);
    }

    /**
     * Get the appropriate forgot password route.
     */
    protected function getLoginRedirectRoute(Request $request): string
    {
        // Check if this is an admin route
        if (str_contains($request->path(), 'admin')) {
            return route('admin.forgot-password');
        }
        
        return route('user.forgot-password');
    }
}
