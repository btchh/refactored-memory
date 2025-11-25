<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\Auth\UserAuthService;
use App\Services\Auth\OtpService;
use App\Services\User\UserManagementService;
use App\Services\User\UserProfileService;
use App\Http\Requests\User\Login;
use App\Http\Requests\User\Register;
use App\Http\Requests\User\SendRegistrationOtp;
use App\Http\Requests\User\SendPasswordResetOtp;
use App\Http\Requests\User\VerifyPasswordResetOtp;
use App\Http\Requests\User\PasswordReset;

class AuthController extends Controller
{
    use \App\Traits\Responses;

    public function __construct(
        private UserAuthService $userAuthService,
        private OtpService $otpService,
        private UserManagementService $userManagementService,
        private UserProfileService $userProfileService,
    ) {}

    /**
     * Show user login form
     */
    public function showLogin()
    {
        // Redirect if already logged in as user
        if (Auth::guard('web')->check()) {
            return redirect()->route('user.dashboard');
        }

        // Redirect if logged in as admin
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('user.auth.login');
    }

    /**
     * Handle user login
     */
    public function login(Login $request)
    {
        $remember = $request->boolean('remember', false);

        $result = $this->userAuthService->login($request->username, $request->password, $remember);

        if ($result['success']) {
            return redirect()->route('user.dashboard')->with('success', $result['message']);
        }

        return redirect()->back()->withInput()->with('error', $result['message']);
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        $this->userAuthService->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('user.login')
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT')
            ->with('success', 'Logged out successfully');
    }

    /**
     * Show user registration form
     */
    public function showRegister()
    {
        return view('user.auth.register');
    }

    /**
     * Send registration OTP
     */
    public function sendRegistrationOtp(SendRegistrationOtp $request)
    {
        try {
            $result = $this->otpService->sendRegistrationOtp($request->phone, $request->email);

            if ($result['success']) {
                return $this->successResponse($result['message']);
            }

            return $this->errorResponse($result['message'], [], 400);
        } catch (\Exception $e) {
            Log::error('Failed to send registration OTP: ' . $e->getMessage());
            return $this->errorResponse('Failed to send OTP', [], 500);
        }
    }

    /**
     * Verify OTP before registration
     */
    public function verifyRegistrationOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'otp' => 'required|digits:6'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validation failed',
                $validator->errors()->toArray(),
                422
            );
        }

        try {
            $result = $this->otpService->verifyOtp($request->phone, $request->otp);

            // If OTP verification succeeds, mark it as verified in cache (valid for 10 minutes)
            if ($result['success']) {
                Cache::put('registration_otp_verified_' . $request->phone, true, 600);
                return $this->successResponse($result['message']);
            }

            return $this->errorResponse($result['message'], [], 400);
        } catch (\Exception $e) {
            Log::error('Failed to verify registration OTP: ' . $e->getMessage());
            return $this->errorResponse('Failed to verify OTP', [], 500);
        }
    }

    /**
     * Register user with OTP verification
     */
    public function register(Register $request)
    {
        try {
            // Check if OTP was verified for this phone number
            $verified = Cache::get('registration_otp_verified_' . $request->phone);

            if (!$verified) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please verify your phone number with OTP before completing registration.'
                    ], 400);
                }
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Please verify your phone number with OTP before completing registration.');
            }

            // Create the user
            $result = $this->userManagementService->createUser($request->validated());

            // Clear the verification cache after successful registration
            Cache::forget('registration_otp_verified_' . $request->phone);

            // Log the user in automatically after successful registration
            Auth::guard('web')->login($result['user']);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registration successful! Welcome to your dashboard.',
                    'redirect' => route('user.dashboard')
                ]);
            }

            return redirect()->route('user.dashboard')->with('success', 'Registration successful! Welcome to your dashboard.');
        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword()
    {
        return view('user.auth.forgot-password');
    }

    /**
     * Send password reset OTP
     */
    public function sendPasswordResetOtp(SendPasswordResetOtp $request)
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
    public function verifyPasswordResetOtp(VerifyPasswordResetOtp $request)
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
            $this->userProfileService->resetPassword($request->phone, $request->password);

            // Clear the verification cache after successful password reset
            Cache::forget('password_reset_otp_verified_' . $request->phone);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password has been reset successfully. Redirecting to login...',
                    'redirect' => route('user.login') . '?reset=success'
                ]);
            }

            return redirect()->route('user.login')->with('success', 'Password has been reset successfully.');
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
