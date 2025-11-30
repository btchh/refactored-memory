<?php

namespace App\Services;

use App\Models\Admin;
use App\Services\MessageService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminService
{
    public function __construct(
        private MessageService $messageService
    ) {}

    //create admin
    public function createAdmin(array $data): array
    {
        $admin = Admin::create([
            'admin_name' => $data['admin_name'],
            'fname' => $data['fname'],
            'lname' => $data['lname'],
            'address' => $data['address'],
            'branch_address' => $data['branch_address'] ?? null,
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        //send welcome message
        try {
            Log::info("Sending welcome message to admin: {$admin->phone}");
            $this->messageService->sendWelcomeMessage($admin->phone, [
                'contact_number' => 'Place_Holder'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send welcome message to admin: {$admin->phone}. Error: " . $e->getMessage());
        }

        return [
            'admin' => $admin,
            'message' => 'Admin created successfully.'
        ];
    }

    //update admin
    public function updateAdmin(int $adminId, array $data): Admin
    {
        $admin = Admin::find($adminId);

        if (!$admin) {
            throw new \Exception("Admin not found.");
        }

        $admin->update($data);
        return $admin->fresh();
    }

    //send OTP for admin creation
    public function sendOtp(string $phone, string $email): array
    {
        //send otp via sms
        $result = $this->messageService->sendVerificationOtp($phone);

        Log::info("Admin OTP Send Result: " . json_encode($result));

        if (isset($result['status']) && $result['status'] === 'success') {
            return [
                'success' => true,
                'message' => $result['message'] ?? 'OTP sent successfully to your phone.',
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to send OTP. ' . ($result['message'] ?? 'Please try again.'),
        ];
    }

    //verify otp for admin creation
    public function verifyOtp(string $phone, string $otp): array
    {
        $result = $this->messageService->verifyOtp($phone, $otp);
        
        Log::info("Admin OTP Verification Result: " . json_encode($result));
        
        // Normalize response format
        if (isset($result['status']) && $result['status'] === 'success') {
            return [
                'success' => true,
                'message' => $result['message'] ?? 'OTP verified successfully.',
            ];
        }
        
        return [
            'success' => false,
            'message' => $result['message'] ?? 'Invalid or expired OTP code.',
        ];
    }

    //change password
    public function changePassword(int $adminId, string $currentPassword, string $newPassword): bool
    {
        $admin = Admin::find($adminId);

        //verify
        if (!Hash::check($currentPassword, $admin->password)) {
            throw new \Exception("Current password is incorrect.");
        }

        if (Hash::check($newPassword, $admin->password)) {
            throw new \Exception("New password cannot be the same as the current password.");
        }

        //update
        $admin->update([
            'password' => Hash::make($newPassword)
        ]);

        return true;
    }

    //select by id
    public function findAdmin(int $adminId): ?Admin
    {
        return Admin::find($adminId);
    }

    //select by email
    public function findAdminByEmail(string $email): ?Admin
    {
        return Admin::where('email', $email)->first();
    }
}
