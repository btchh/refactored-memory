<?php

namespace App\Services\Auth;

use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthService
{
    protected AdminRepositoryInterface $adminRepository;

    public function __construct(AdminRepositoryInterface $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    /**
     * Attempt admin login
     *
     * @param string $loginField Email or admin_name
     * @param string $password
     * @param bool $remember
     * @return array
     */
    public function login(
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

    /**
     * Admin logout
     *
     * @return bool
     */
    public function logout(): bool
    {
        /** @var \App\Models\Admin $admin */
        $admin = Auth::guard('admin')->user();

        if ($admin && !empty($admin->remember_token)) {
            $admin->remember_token = null;
            $admin->save();
        }

        Auth::guard('admin')->logout();

        return true;
    }

    /**
     * Login admin by ID
     *
     * @param int $adminId
     * @return bool
     * @throws \Exception
     */
    public function loginById(int $adminId): bool
    {
        $admin = $this->adminRepository->find($adminId);

        if (!$admin) {
            throw new \Exception("Admin not found.");
        }

        Auth::guard('admin')->login($admin);

        return true;
    }
}
