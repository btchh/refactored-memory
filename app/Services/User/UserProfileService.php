<?php

namespace App\Services\User;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserProfileService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * Change user password
     *
     * @param int $userId User ID
     * @param string $currentPassword Current password for verification
     * @param string $newPassword New password to set
     * @return bool True if password change was successful
     * @throws \Exception If user not found, current password is incorrect, or new password is same as current
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new \Exception("User not found.");
        }

        if (!Hash::check($currentPassword, $user->password)) {
            throw new \Exception("Current password is incorrect.");
        }

        if (Hash::check($newPassword, $user->password)) {
            throw new \Exception("New password cannot be the same as the current password.");
        }

        // Update password
        $this->userRepository->update($userId, [
            'password' => Hash::make($newPassword)
        ]);

        return true;
    }

    /**
     * Reset user password (used after OTP verification)
     *
     * @param string $phone Phone number of the user
     * @param string $password New password to set
     * @return bool True if password reset was successful
     * @throws \Exception If user not found
     */
    public function resetPassword(string $phone, string $password): bool
    {
        $user = $this->userRepository->findByPhone($phone);

        if (!$user) {
            throw new \Exception("User not found.");
        }

        // Update password
        $this->userRepository->update($user->id, [
            'password' => Hash::make($password)
        ]);

        return true;
    }
}
