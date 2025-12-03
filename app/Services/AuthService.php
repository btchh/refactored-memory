<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    //attempt user login
    public function loginUser(
        string $loginField,
        string $password,
        bool $remember = false
    ): array {
        $feildType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::guard('web')->attempt([$feildType => $loginField, 'password' => $password], $remember)) {
            $user = Auth::guard('web')->user();
            return [
                'success' => true,
                'message' => 'Login Success',
                'user' => $user
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid Credentials',
        ];
    }

    //attempt admin login
    public function loginAdmin(
        string $loginField,
        string $password,
        bool $remember = false
    ): array {
        $feildType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'admin_name';

        if (Auth::guard('admin')->attempt([$feildType => $loginField, 'password' => $password], $remember)) {
            $admin = Auth::guard('admin')->user();
            
            // Log the login action
            $this->auditService->logLogin();
            
            return [
                'success' => true,
                'message' => 'Login Success',
                'admin' => $admin
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid Credentials',
        ];
    }

    //user logout
    public function logoutUser(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::guard('web')->user();

        // Clear remember token if it exists
        if ($user && !empty($user->remember_token)) {
            $user->remember_token = null;
            $user->save();
        }

        Auth::guard('web')->logout();

        return true;
    }

    //admin logout
    public function logoutAdmin(): bool
    {
        /** @var \App\Models\Admin @admin */
        $admin = Auth::guard('admin')->user();

        // Log the logout action before logging out
        $this->auditService->logLogout();

        if ($admin && !empty($admin->remember_token)) {
            $admin->remember_token = null;
            $admin->save();
        }

        Auth::guard('admin')->logout();

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
