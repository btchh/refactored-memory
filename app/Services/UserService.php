<?php

namespace App\Services;

use App\Models\User;
use App\Services\MessageService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;



class UserService
{
    public function __construct(
        private MessageService $messageService
    ) {}

    //create user
    public function createUser(array $data): array
    {
        // Re-validate uniqueness to prevent race conditions
        if (User::where('email', $data['email'])->exists()) {
            throw new \Exception('Email is already registered');
        }

        if (User::where('phone', $data['phone'])->exists()) {
            throw new \Exception('Phone number is already registered');
        }

        if (User::where('username', $data['username'])->exists()) {
            throw new \Exception('Username is already taken');
        }

        $user = User::create([
            'username' => $data['username'],
            'fname' => $data['fname'],
            'lname' => $data['lname'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        //send welcome message
        try {
            Log::info("User create with phone: {$user->phone}");
            $this->messageService->sendWelcomeMessage($user->phone, [
                'contact_number' => 'Place_Holder'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send welcome message to user: {$user->phone}. Error: " . $e->getMessage());
        }

        return [
            'user' => $user,
            'message' => 'User registered successfully.'
        ];
    }

    //update user
    public function updateUser(int $userId, array $data): User
    {
        $user = User::find($userId);

        if (!$user) {
            throw new \Exception("User not found.");
        }

        $user->update($data);
        return $user->fresh();
    }

    //change password
    public function changePass(int $userId, string $currentPassword, string $newPassword): bool
    {
        $user = User::find($userId);

        if (!$user) {
            throw new \Exception("User not found.");
        }

        if (!Hash::check($currentPassword, $user->password)) {
            throw new \Exception("Current password is incorrect.");
        }

        if (Hash::check($newPassword, $user->password)) {
            throw new \Exception("New password cannot be the same as the current password.");
        }

        //update
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        return true;
    }

    //password reset process / send password reset otp
    public function initiatePassReset(string $phone): array
    {
        //check if user exists with this $phone
        $user = User::where('phone', $phone)->first();

        //to not reveal whether the phone exists, we return success either way
        if (!$user) {
            return [
                'success' => true,
                'message' => 'If the phone number exists in our system, an OTP has been sent.'
            ];
        }

        //send OTP via SMS
        $result = $this->messageService->sendPasswordResetOtp($phone);

        Log::info("Password Reset OTP Send Result: {$result}");

        if (isset($result['success']) && $result['success'] === true) {
            return [
                'success' => true,
                'message' => 'If the phone number exists in our system, an OTP has been sent.'
            ];
        }

        return [
            'success' => false,
            'message' => 'Password reset process initiated.'
        ];
    }

    //complete password reset with verified otp
    public function completePassReset(string $phone, string $password): bool
    {
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            throw new \Exception("User not found.");
        }

        //update password
        $user->update([
            'password' => Hash::make($password)
        ]);

        return true;
    }

    //send Registration OTP
    public function sendRegistrationOtp(string $phone, string $email): array
    {
        //send otp via sms
        $result = $this->messageService->sendVerificationOtp($phone);

        Log::info("Registration OTP Send Result: {$result}");

        if (isset($result['status']) && $result['status'] === 'success') {
            return [
                'success' => true,
                'message' => 'OTP sent successfully.',
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to send OTP. ' . ($result['message'] ?? 'Please try again.'),
        ];
    }

    //verify otp
    public function verifyOtp(string $phone, string $otp): array
    {
        return $this->messageService->verifyOtp($phone, $otp);
    }

    //generate reset password token
    public function generateResetToken(): string
    {
        return Str::random(128);
    }
}
