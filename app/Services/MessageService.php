<?php

namespace App\Services;

class MessageService
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Replace placeholders in message template with actual values
     */
    private function replaceVariables(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            $template = str_replace(":{$key}", $value, $template);
        }
        return $template;
    }

    /**
     * Send OTP verification SMS
     */
    public function sendVerificationOtp(string $phoneNumber): array
    {
        return $this->smsService->sendOtp(
            $phoneNumber,
            config('messages.otp.verification')
        );
    }

    /**
     * Send password reset OTP
     */
    public function sendPasswordResetOtp(string $phoneNumber): array
    {
        return $this->smsService->sendOtp(
            $phoneNumber,
            config('messages.otp.password_reset')
        );
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(string $phoneNumber, string $otp): array
    {
        return $this->smsService->verifyOtp($phoneNumber, $otp);
    }

    /**
     * Send booking confirmation SMS
     */
    public function sendBookingConfirmed(string $phoneNumber, array $data): array
    {
        $message = $this->replaceVariables(
            config('messages.booking.confirmed'),
            $data
        );
        return $this->smsService->sendSms($phoneNumber, $message);
    }

    /**
     * Send laundry arrived notification
     */
    public function sendLaundryArrived(string $phoneNumber, array $data): array
    {
        $message = $this->replaceVariables(
            config('messages.booking.laundry_arrived'),
            $data
        );
        return $this->smsService->sendSms($phoneNumber, $message);
    }

    /**
     * Send laundry completed notification
     */
    public function sendLaundryCompleted(string $phoneNumber, array $data): array
    {
        $message = $this->replaceVariables(
            config('messages.booking.laundry_completed'),
            $data
        );
        return $this->smsService->sendSms($phoneNumber, $message);
    }

    /**
     * Send ready for pickup notification
     */
    public function sendReadyForPickup(string $phoneNumber, array $data): array
    {
        $message = $this->replaceVariables(
            config('messages.booking.ready_for_pickup'),
            $data
        );
        return $this->smsService->sendSms($phoneNumber, $message);
    }

    /**
     * Send out for delivery notification
     */
    public function sendOutForDelivery(string $phoneNumber, array $data): array
    {
        $message = $this->replaceVariables(
            config('messages.booking.out_for_delivery'),
            $data
        );
        return $this->smsService->sendSms($phoneNumber, $message);
    }

    /**
     * Send delivered notification
     */
    public function sendDelivered(string $phoneNumber, array $data): array
    {
        $message = $this->replaceVariables(
            config('messages.booking.delivered'),
            $data
        );
        return $this->smsService->sendSms($phoneNumber, $message);
    }

    /**
     * Send booking cancelled notification (by customer)
     */
    public function sendBookingCancelled(string $phoneNumber, array $data): array
    {
        $message = $this->replaceVariables(
            config('messages.booking.cancelled'),
            $data
        );
        return $this->smsService->sendSms($phoneNumber, $message);
    }

    /**
     * Send booking cancelled by admin notification
     */
    public function sendBookingCancelledByAdmin(string $phoneNumber, array $data): array
    {
        $message = $this->replaceVariables(
            config('messages.booking.cancelled_by_admin'),
            $data
        );
        return $this->smsService->sendSms($phoneNumber, $message);
    }

    /**
     * Send booking rescheduled notification
     */
    public function sendBookingRescheduled(string $phoneNumber, array $data): array
    {
        $message = $this->replaceVariables(
            config('messages.booking.rescheduled'),
            $data
        );
        return $this->smsService->sendSms($phoneNumber, $message);
    }

    /**
     * Send pickup reminder (tomorrow)
     */
    public function sendPickupReminder(string $phoneNumber, array $data): array
    {
        $message = $this->replaceVariables(
            config('messages.reminder.pickup_tomorrow'),
            $data
        );
        return $this->smsService->sendSms($phoneNumber, $message);
    }

    /**
     * Send delivery reminder (tomorrow)
     */
    public function sendDeliveryReminder(string $phoneNumber, array $data): array
    {
        $message = $this->replaceVariables(
            config('messages.reminder.delivery_tomorrow'),
            $data
        );
        return $this->smsService->sendSms($phoneNumber, $message);
    }

    /**
     * Send queue status update
     */
    public function sendQueueStatusUpdate(string $phoneNumber, array $data): array
    {
        $message = $this->replaceVariables(
            config('messages.queue.status_update'),
            $data
        );
        return $this->smsService->sendSms($phoneNumber, $message);
    }

    /**
     * Send now processing notification
     */
    public function sendNowProcessing(string $phoneNumber, array $data): array
    {
        $message = $this->replaceVariables(
            config('messages.queue.now_processing'),
            $data
        );
        return $this->smsService->sendSms($phoneNumber, $message);
    }

    /**
     * Send payment reminder
     */
    public function sendPaymentReminder(string $phoneNumber, array $data): array
    {
        $message = $this->replaceVariables(
            config('messages.payment.payment_reminder'),
            $data
        );
        return $this->smsService->sendSms($phoneNumber, $message);
    }

    /**
     * Send payment received confirmation
     */
    public function sendPaymentReceived(string $phoneNumber, array $data): array
    {
        $message = $this->replaceVariables(
            config('messages.payment.payment_received'),
            $data
        );
        return $this->smsService->sendSms($phoneNumber, $message);
    }

    /**
     * Send welcome message to new users
     */
    public function sendWelcomeMessage(string $phoneNumber, array $data): array
    {
        $message = $this->replaceVariables(
            config('messages.general.welcome'),
            $data
        );
        return $this->smsService->sendSms($phoneNumber, $message);
    }

    /**
     * Send password reset message with temporary password
     */
    public function sendPasswordResetMessage(string $phoneNumber, array $data): array
    {
        $message = $this->replaceVariables(
            config('messages.general.password_reset'),
            $data
        );
        return $this->smsService->sendSms($phoneNumber, $message);
    }

    /**
     * Send thank you message
     */
    public function sendThankYou(string $phoneNumber): array
    {
        $message = config('messages.general.thank_you');
        return $this->smsService->sendSms($phoneNumber, $message);
    }

    /**
     * Send bulk SMS with custom message
     */
    public function sendBulkMessage(array $phoneNumbers, string $messageKey, array $data = []): array
    {
        $template = config($messageKey);
        $message = $this->replaceVariables($template, $data);
        return $this->smsService->sendBulkSms($phoneNumbers, $message);
    }
}
