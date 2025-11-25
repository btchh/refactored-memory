<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\Auth\AdminAuthService;
use App\Services\Auth\OtpService;
use App\Services\Admin\AdminProfileService;
use App\Http\Requests\Admin\Login;
use App\Http\Requests\Admin\SendPasswordReset;
use App\Http\Requests\Admin\PasswordReset;
use App\Http\Requests\Admin\VerifyPasswordReset;

class AuthController extends Controller
{
    use \App\Traits\Responses;

    public function __construct(
        private AdminAuthService $adminAuthService,
        private OtpService $otpService,
        private AdminProfileService $adminProfileService,
    ) {}

    /**
     * Show admin login form
     */
    public function showLogin()
    {
        // Redirect if already logged in as admin
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        // Redirect if logged in as user
        if (Auth::guard('web')->check()) {
            return redirect()->route('user.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle admin login
     */
    public function login(Login $request)
    {
        $remember = $request->boolean('remember', false);
        $loginField = $request->admin_name;

        $result = $this->adminAuthService->login($loginField, $request->password, $remember);

        if ($result['success']) {
            return redirect()->route('admin.dashboard')->with('success', $result['message']);
        }

        return redirect()->back()->withInput()->with('error', $result['message']);
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        $this->adminAuthService->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT')
            ->with('success', 'Logged out successfully');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword()
    {
        return view('admin.auth.forgot-password');
    }

    /**
     * Send password reset OTP
     */
    public function sendPasswordReset(SendPasswordReset $request)
    {
        try {
            $result = $this->otpService->sendPasswordResetOtp($request->phone);

            if ($result['success']) {
                return $this->successResponse($result['message']);
            }

            return $this->errorResponse($result['message'], [], 400);
        } catch (\Exception $e) {
            Log::error('Failed to send password reset OTP: ' . $e->getMessage());
            return $this->errorResponse('Failed to send OTP', [], 500);
        }
    }

    /**
     * Verify password reset OTP (Step 2)
     */
    public function verifyPasswordReset(VerifyPasswordReset $request)
    {
        try {
            $result = $this->otpService->verifyOtp($request->phone, $request->otp);

            // If OTP verification succeeds, mark it as verified in cache (valid for 10 minutes)
            if ($result['success']) {
                Cache::put('password_reset_otp_verified_' . $request->phone, true, 600);
                return $this->successResponse($result['message']);
            }

            return $this->errorResponse($result['message'], [], 400);
        } catch (\Exception $e) {
            Log::error('Failed to verify password reset OTP: ' . $e->getMessage());
            return $this->errorResponse('Failed to verify OTP', [], 500);
        }
    }

    /**
     * Reset password (Step 3)
     */
    public function resetPassword(PasswordReset $request)
    {
        try {
            // Check if OTP was verified for this phone number
            $verified = Cache::get('password_reset_otp_verified_' . $request->phone);

            if (!$verified) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please verify OTP first before resetting password.'
                    ], 400);
                }
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Please verify OTP first before resetting password.');
            }

            // Reset the password
            $this->adminProfileService->resetPassword($request->phone, $request->password);

            // Clear the verification cache after successful password reset
            Cache::forget('password_reset_otp_verified_' . $request->phone);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password has been reset successfully. Redirecting to login...',
                    'redirect' => route('admin.login') . '?reset=success'
                ]);
            }

            return redirect()->route('admin.login')->with('success', 'Password has been reset successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to reset password: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
