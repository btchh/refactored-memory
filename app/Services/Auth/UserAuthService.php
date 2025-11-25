<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserAuthService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Attempt user login
     *
     * @param string $loginField Email or username
     * @param string $password
     * @param bool $remember
     * @return array
     */
    public function login(
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

    /**
     * User logout
     *
     * @return bool
     */
    public function logout(): bool
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

    /**
     * Login user by ID
     *
     * @param int $userId
     * @return bool
     * @throws \Exception
     */
    public function loginById(int $userId): bool
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new \Exception("User not found.");
        }

        Auth::guard('web')->login($user);

        return true;
    }
}
