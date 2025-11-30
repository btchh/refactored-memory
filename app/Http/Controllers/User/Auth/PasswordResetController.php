<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\SendPasswordResetOtp;
use App\Http\Requests\User\VerifyPasswordResetOtp;
use App\Http\Requests\User\PasswordReset;
use App\Services\UserService;

class PasswordResetController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    /**
     * Show forgot password form
     */
    public function showForgotPassword()
    {
        return view('user.forgot-password');
    }

    /**
     * Send password reset OTP
     */
    public function sendPasswordResetOtp(SendPasswordResetOtp $request)
    {
        try {
            $result = $this->userService->initiatePassReset($request->phone);

            return redirect()->back()->with('success', $result['message']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Verify password reset OTP and reset password
     */
    public function verifyPasswordResetOtp(VerifyPasswordResetOtp $request)
    {
        try {
            $otpResult = $this->userService->verifyOtp($request->phone, $request->otp);

            if (!$otpResult['success']) {
                return redirect()->back()->with('error', 'Invalid or expired OTP');
            }

            return redirect()->route('user.reset-password', ['phone' => $request->phone])
                ->with('success', 'OTP verified successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show reset password form
     */
    public function showResetPassword($phone)
    {
        return view('user.forgot-password', ['phone' => $phone]);
    }

    /**
     * Reset password
     */
    public function resetPassword(PasswordReset $request)
    {
        try {
            $this->userService->completePassReset($request->phone, $request->password);

            return redirect()->route('user.login')->with('success', 'Password has been reset successfully. You can now login with your new password.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
