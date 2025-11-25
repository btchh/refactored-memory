<?php

namespace App\Services\Auth;

use App\Services\MessageService;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public function __construct(
        private MessageService $messageService
    ) {}

    /**
     * Send registration OTP to phone number
     * 
     * @param string $phone Phone number to send OTP to
     * @param string $email Email address (for logging/tracking purposes)
     * @return array Response with success status and message
     */
    public function sendRegistrationOtp(string $phone, string $email): array
    {
        // Send OTP via SMS
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

    /**
     * Send password reset OTP to phone number
     * Returns success regardless of whether phone exists for security
     * 
     * @param string $phone Phone number to send OTP to
     * @return array Response with success status and message
     */
    public function sendPasswordResetOtp(string $phone): array
    {
        // Send OTP via SMS
        $result = $this->messageService->sendVerificationOtp($phone);

        Log::info("Password Reset OTP Send Result: " . json_encode($result));

        // Always return success to not reveal if phone exists
        if (isset($result['status']) && $result['status'] === 'success') {
            return [
                'success' => true,
                'message' => 'If the phone number exists in our system, an OTP has been sent.'
            ];
        }

        // Even if SMS fails, return success for security
        return [
            'success' => true,
            'message' => 'If the phone number exists in our system, an OTP has been sent.'
        ];
    }

    /**
     * Verify OTP code for a phone number
     * 
     * @param string $phone Phone number to verify OTP for
     * @param string $otp OTP code to verify
     * @return array Response with success status and message
     */
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
}
