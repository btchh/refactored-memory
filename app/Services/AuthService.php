<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    //attempt user login
    public function loginUser(
        string $loginField,
        string $password,
        bool $remember = false
    ): array {
        $fieldType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::guard('web')->attempt([$fieldType => $loginField, 'password' => $password], $remember)) {
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
        $fieldType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'admin_name';

        if (Auth::guard('admin')->attempt([$fieldType => $loginField, 'password' => $password], $remember)) {
            $admin = Auth::guard('admin')->user();
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
