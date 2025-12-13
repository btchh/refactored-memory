<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $auditService;
    protected $loginAttemptService;

    public function __construct(AuditService $auditService, LoginAttemptService $loginAttemptService)
    {
        $this->auditService = $auditService;
        $this->loginAttemptService = $loginAttemptService;
    }

    //attempt user login
    public function loginUser(
        string $loginField,
        string $password,
        bool $remember = false,
        ?string $ipAddress = null
    ): array {
        $feildType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $ipAddress = $ipAddress ?? request()->ip();

        // Check current state - only block for obvious spam attacks
        $currentState = $this->loginAttemptService->checkState($loginField, $ipAddress, 'login');
        
        // Only hard block for spam attacks (15+ attempts)
        if ($currentState['action'] === 'block') {
            return [
                'success' => false,
                'message' => $currentState['message'],
                'reason' => 'spam_blocked',
                'action' => 'block',
            ];
        }

        // First check if user exists (including soft-deleted users)
        $user = User::withTrashed()->where($feildType, $loginField)->first();
        
        if ($user && Hash::check($password, $user->password)) {
            // Check if user is archived (soft deleted)
            if ($user->trashed()) {
                return [
                    'success' => false,
                    'message' => 'Your account has been suspended. You cannot access the site at this time. Please contact support for assistance.',
                    'reason' => 'archived'
                ];
            }
            
            // Check if user is disabled
            if ($user->status === 'disabled') {
                return [
                    'success' => false,
                    'message' => 'Your account has been temporarily disabled. Please contact support to reactivate your account.',
                    'reason' => 'disabled'
                ];
            }
            
            // User is active, proceed with login
            if (Auth::guard('web')->attempt([$feildType => $loginField, 'password' => $password], $remember)) {
                // Clear failed attempts on successful login
                $this->loginAttemptService->clearAttempts($loginField, $ipAddress);
                
                $user = Auth::guard('web')->user();
                return [
                    'success' => true,
                    'message' => 'Login Success',
                    'user' => $user
                ];
            }
        }

        // Record failed attempt and get warning state
        $attemptState = $this->loginAttemptService->recordFailedAttempt($loginField, $ipAddress);

        // Return with appropriate warning/redirect action
        return [
            'success' => false,
            'message' => 'Invalid Credentials',
            'attempt_warning' => $attemptState,
        ];
    }

    //attempt admin login
    public function loginAdmin(
        string $loginField,
        string $password,
        bool $remember = false,
        ?string $ipAddress = null
    ): array {
        $feildType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $ipAddress = $ipAddress ?? request()->ip();

        // Check current state - only block for obvious spam attacks
        $currentState = $this->loginAttemptService->checkState($loginField, $ipAddress, 'login');
        
        // Only hard block for spam attacks (15+ attempts)
        if ($currentState['action'] === 'block') {
            return [
                'success' => false,
                'message' => $currentState['message'],
                'reason' => 'spam_blocked',
                'action' => 'block',
            ];
        }

        if (Auth::guard('admin')->attempt([$feildType => $loginField, 'password' => $password], $remember)) {
            // Clear failed attempts on successful login
            $this->loginAttemptService->clearAttempts($loginField, $ipAddress);
            
            $admin = Auth::guard('admin')->user();
            
            // Log the login action
            $this->auditService->logLogin();
            
            return [
                'success' => true,
                'message' => 'Login Success',
                'admin' => $admin
            ];
        }

        // Record failed attempt and get warning state
        $attemptState = $this->loginAttemptService->recordFailedAttempt($loginField, $ipAddress);

        // Return with appropriate warning/redirect action
        return [
            'success' => false,
            'message' => 'Invalid Credentials',
            'attempt_warning' => $attemptState,
        ];
    }

    //user logout
    public function logoutUser(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::guard('web')->user();

        if ($user) {
            // Clear remember token
            $user->remember_token = null;
            $user->save();
        }

        // Logout and forget the user completely
        Auth::guard('web')->logout();
        
        // Also logout from admin guard if somehow logged in
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        return true;
    }

    //admin logout
    public function logoutAdmin(): bool
    {
        /** @var \App\Models\Admin $admin */
        $admin = Auth::guard('admin')->user();

        // Log the logout action before logging out
        $this->auditService->logLogout();

        if ($admin) {
            // Clear remember token
            $admin->remember_token = null;
            $admin->save();
        }

        // Logout and forget the admin completely
        Auth::guard('admin')->logout();
        
        // Also logout from web guard if somehow logged in
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        return true;
    }

    //login by id
    public function loginById(int $userId): bool
    {
        $user = User::find($userId);

        if (!$user) {
            throw new \Exception("User not found.");
        }

        Auth::guard('web')->login($user);

        return true;
    }
}
