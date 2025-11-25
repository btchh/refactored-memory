<?php

namespace App\Services\Admin;

use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class AdminProfileService
{
    public function __construct(
        private AdminRepositoryInterface $adminRepository
    ) {}

    /**
     * Change admin password
     *
     * @param int $adminId Admin ID
     * @param string $currentPassword Current password for verification
     * @param string $newPassword New password to set
     * @return bool True if password change was successful
     * @throws \Exception If admin not found, current password is incorrect, or new password is same as current
     */
    public function changePassword(int $adminId, string $currentPassword, string $newPassword): bool
    {
        $admin = $this->adminRepository->find($adminId);

        if (!$admin) {
            throw new \Exception("Admin not found with ID: {$adminId}");
        }

        //verify current password
        if (!Hash::check($currentPassword, $admin->password)) {
            throw new \Exception("Current password is incorrect.");
        }

        //check if new password is same as current
        if (Hash::check($newPassword, $admin->password)) {
            throw new \Exception("New password cannot be the same as the current password.");
        }

        //update password
        $this->adminRepository->update($adminId, [
            'password' => Hash::make($newPassword)
        ]);

        return true;
    }

    /**
     * Complete password reset with verified OTP
     *
     * @param string $phone Phone number of the admin
     * @param string $password New password to set
     * @return bool True if password reset was successful
     * @throws \Exception If admin not found
     */
    public function resetPassword(string $phone, string $password): bool
    {
        $admin = $this->adminRepository->findByPhone($phone);

        if (!$admin) {
            throw new \Exception("Admin not found.");
        }

        //update password
        $this->adminRepository->update($admin->id, [
            'password' => Hash::make($password)
        ]);

        return true;
    }
}
