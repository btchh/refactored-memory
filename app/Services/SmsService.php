<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private ?string $apiToken;
    private string $baseUrl = 'https://sms.iprogtech.com/api/v1';

    public function __construct()
    {
        $this->apiToken = config('services.iprog_sms.api_token');
        
        if (empty($this->apiToken)) {
            throw new \Exception('IPROG SMS API token is not configured. Please set IPROG_SMS_API_TOKEN in your .env file.');
        }
    }

    /**
     * Send SMS to a single recipient
     *
     * @param string $phoneNumber
     * @param string $message
     * @param int $smsProvider (0 or 1)
     * @return array
     */
    public function sendSms(string $phoneNumber, string $message, int $smsProvider = 0): array
    {
        try {
            Log::info('SMS Service - Sending SMS to: ' . $phoneNumber);
            Log::info('SMS Service - Message: ' . $message);
            
            $payload = [
                'api_token' => $this->apiToken,
                'phone_number' => $phoneNumber,
                'message' => $message,
                'sms_provider' => $smsProvider,
            ];
            
            Log::info('SMS Service - Payload:', $payload);
            
            $response = Http::withOptions(['verify' => false])
                ->post("{$this->baseUrl}/sms_messages", $payload);

            $result = $response->json();
            Log::info('SMS Service - Response:', $result ?? ['raw' => $response->body()]);
            
            return $result;
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to send SMS: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send bulk SMS to multiple recipients
     *
     * @param array|string $phoneNumbers (comma-separated string or array)
     * @param string $message
     * @param int $smsProvider (0 or 1)
     * @return array
     */
    public function sendBulkSms($phoneNumbers, string $message, int $smsProvider = 0): array
    {
        try {
            // Convert array to comma-separated string if needed
            if (is_array($phoneNumbers)) {
                $phoneNumbers = implode(',', $phoneNumbers);
            }

            $response = Http::withOptions(['verify' => false])
                ->post("{$this->baseUrl}/sms_messages/send_bulk", [
                    'api_token' => $this->apiToken,
                    'phone_number' => $phoneNumbers,
                    'message' => $message,
                    'sms_provider' => $smsProvider,
                ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Bulk SMS sending failed: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to send bulk SMS: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check SMS status by message ID
     *
     * @param string $messageId
     * @return array
     */
    public function checkStatus(string $messageId): array
    {
        try {
            $response = Http::withOptions(['verify' => false])
                ->get("{$this->baseUrl}/sms_messages/status", [
                    'api_token' => $this->apiToken,
                    'message_id' => $messageId,
                ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('SMS status check failed: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to check SMS status: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check available SMS credits
     *
     * @return array
     */
    public function checkCredits(): array
    {
        try {
            $response = Http::withOptions(['verify' => false])
                ->get("{$this->baseUrl}/account/sms_credits", [
                    'api_token' => $this->apiToken,
                ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('SMS credits check failed: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to check SMS credits: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send OTP to a phone number
     *
     * @param string $phoneNumber
     * @param string|null $customMessage (use :otp placeholder for OTP code)
     * @return array
     */
    public function sendOtp(string $phoneNumber, ?string $customMessage = null): array
    {
        try {
            $payload = [
                'api_token' => $this->apiToken,
                'phone_number' => $phoneNumber,
            ];

            if ($customMessage) {
                $payload['message'] = $customMessage;
            }

            Log::info('Sending OTP to: ' . $phoneNumber);
            Log::info('Payload:', $payload);

            $response = Http::withOptions(['verify' => false])
                ->post("{$this->baseUrl}/otp/send_otp", $payload);

            $result = $response->json();
            
            Log::info('OTP API Response:', $result ?? ['raw' => $response->body()]);
            Log::info('HTTP Status Code: ' . $response->status());

            return $result ?? [
                'status' => 'error',
                'message' => 'Empty response from API',
                'raw_response' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('OTP sending failed: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to send OTP: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify OTP code
     *
     * @param string $phoneNumber
     * @param string $otp
     * @return array
     */
    public function verifyOtp(string $phoneNumber, string $otp): array
    {
        try {
            $response = Http::withOptions(['verify' => false])
                ->post("{$this->baseUrl}/otp/verify_otp", [
                    'api_token' => $this->apiToken,
                    'phone_number' => $phoneNumber,
                    'otp' => $otp,
                ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('OTP verification failed: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to verify OTP: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get list of OTPs
     *
     * @return array
     */
    public function getOtpList(): array
    {
        try {
            $response = Http::withOptions(['verify' => false])
                ->get("{$this->baseUrl}/otp", [
                    'api_token' => $this->apiToken,
                ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Failed to get OTP list: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to get OTP list: ' . $e->getMessage()
            ];
        }
    }
}
