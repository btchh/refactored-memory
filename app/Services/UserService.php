<?php

namespace App\Services;

use App\Models\User;
use App\Services\MessageService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;



class UserService
{
    public function __construct(
        private MessageService $messageService,
        private GeocodeService $geocodeService
    ) {}

    //create user
    public function createUser(array $data): array
    {
        try {
            // Wrap user creation in database transaction to prevent race conditions
            $result = DB::transaction(function () use ($data) {
                // Re-validate uniqueness constraints inside transaction
                // This ensures atomicity and prevents concurrent registrations
                if (User::where('email', $data['email'])->exists()) {
                    throw new \Exception('Email is already registered');
                }

                if (User::where('phone', $data['phone'])->exists()) {
                    throw new \Exception('Phone number is already registered');
                }

                if (User::where('username', $data['username'])->exists()) {
                    throw new \Exception('Username is already taken');
                }

                // Geocode address if provided
                if (!empty($data['address'])) {
                    $coords = $this->geocodeService->geocodeAddressFresh($data['address']);
                    if ($coords) {
                        $data['latitude'] = $coords['latitude'];
                        $data['longitude'] = $coords['longitude'];
                    }
                }

                $user = User::create([
                    'username' => $data['username'],
                    'fname' => $data['fname'],
                    'lname' => $data['lname'],
                    'address' => $data['address'],
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'latitude' => $data['latitude'] ?? null,
                    'longitude' => $data['longitude'] ?? null,
                ]);

                return $user;
            });

            //send welcome message (outside transaction to avoid blocking)
            try {
                Log::info("User created with phone: {$result->phone}");
                $this->messageService->sendWelcomeMessage($result->phone, [
                    'contact_number' => 'Place_Holder'
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to send welcome message to user: {$result->phone}. Error: " . $e->getMessage());
            }

            return [
                'user' => $result,
                'message' => 'User registered successfully.'
            ];
        } catch (QueryException $e) {
            // Handle database constraint violations
            if ($e->getCode() === '23000') {
                // Integrity constraint violation
                $errorMessage = $e->getMessage();
                
                if (str_contains($errorMessage, 'users_email_unique')) {
                    throw new \Exception('Email is already registered');
                } elseif (str_contains($errorMessage, 'users_phone_unique')) {
                    throw new \Exception('Phone number is already registered');
                } elseif (str_contains($errorMessage, 'users_username_unique')) {
                    throw new \Exception('Username is already taken');
                }
                
                throw new \Exception('Registration failed due to duplicate data');
            }
            
            // Re-throw other database exceptions
            throw $e;
        }
    }

    //update user
    public function updateUser(int $userId, array $data): User
    {
        $user = User::find($userId);

        if (!$user) {
            throw new \Exception("User not found.");
        }

        // If address changed, geocode it
        if (isset($data['address']) && $data['address'] !== $user->address) {
            $coords = $this->geocodeService->geocodeAddressFresh($data['address']);
            if ($coords) {
                $data['latitude'] = $coords['latitude'];
                $data['longitude'] = $coords['longitude'];
            } else {
                $data['latitude'] = null;
                $data['longitude'] = null;
            }
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

        Log::info("Registration OTP Send Result: " . json_encode($result));

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

    //verify otp
    public function verifyOtp(string $phone, string $otp): array
    {
        $result = $this->messageService->verifyOtp($phone, $otp);
        
        Log::info("OTP Verification Result: " . json_encode($result));
        
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

    //generate reset password token
    public function generateResetToken(): string
    {
        return Str::random(128);
    }
}
