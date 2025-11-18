<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\AuthService;
use App\Services\UserService;
use App\Http\Requests\User\Login;
use App\Http\Requests\User\Register;
use App\Http\Requests\User\SendRegistrationOtp;
use App\Http\Requests\User\SendPasswordResetOtp;
use App\Http\Requests\User\VerifyPasswordResetOtp;
use App\Http\Requests\User\PasswordReset;
use App\Http\Requests\User\UpdateProfile;
use App\Http\Requests\User\ChangePassword;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
        private AuthService $authService,
    ) {}

    /**
     * Show user login form
     */
    public function showLogin()
    {
        // Redirect if already logged in as user
        if (Auth::guard('web')->check()) {
            return redirect()->route('users.dashboard');
        }

        // Redirect if logged in as admin
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('user.login');
    }

    /**
     * Handle user login
     */
    public function login(Login $request)
    {
        \Log::info('User login attempt', ['username' => $request->username]);
        
        $remember = $request->boolean('remember', false);

        $result = $this->authService->loginUser($request->username, $request->password, $remember);

        \Log::info('User login result', ['success' => $result['success'], 'message' => $result['message']]);

        if ($result['success']) {
            \Log::info('User redirecting to dashboard');
            return redirect()->route('users.dashboard')->with('success', $result['message']);
        }

        \Log::info('User login failed, redirecting back');
        return redirect()->back()->withInput()->with('error', $result['message']);
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        $this->authService->logoutUser();
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
        return view('user.register');
    }

    /**
     * Send registration OTP
     */
    public function sendRegistrationOtp(SendRegistrationOtp $request)
    {
        try {
            $result = $this->userService->sendRegistrationOtp($request->phone, $request->email);

            if ($result['success']) {
                return redirect()->back()->with('success', $result['message']);
            }

            return redirect()->back()->with('error', $result['message']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Register user with OTP verification
     */
    public function register(Register $request)
    {
        try {
            // Verify OTP first
            $otpResult = $this->userService->verifyOtp($request->phone, $request->otp);

            if (!$otpResult['success']) {
                return redirect()->back()->withInput()->with('error', 'Invalid or expired OTP');
            }

            // Create user
            $result = $this->userService->createUser($request->validated());

            return redirect()->route('user.login')->with('success', 'Registration successful. Please login with your credentials.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

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
        return view('user.reset-password', ['phone' => $phone]);
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

    /**
     * Show user dashboard
     */
    public function showDashboard()
    {
        return view('user.dashboard');
    }

    /**
     * Show user profile
     */
    public function showProfile()
    {
        return view('user.profile');
    }

    /**
     * Show change password form
     */
    public function showChangePassword()
    {
        return view('user.change-password');
    }

    /**
     * Update user profile
     */
    public function updateProfile(UpdateProfile $request)
    {
        $user = Auth::guard('web')->user();

        if (!$user) {
            return redirect()->route('user.login')->with('error', 'Unauthorized');
        }

        try {
            $this->userService->updateUser(
                $user->id,
                $request->only(['username', 'fname', 'lname', 'email', 'phone', 'address'])
            );

            return redirect()->route('user.profile')->with('success', 'Profile updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Change user password
     */
    public function changePassword(ChangePassword $request)
    {
        $user = Auth::guard('web')->user();

        if (!$user) {
            return redirect()->route('user.login')->with('error', 'Unauthorized');
        }

        try {
            $this->userService->changePass(
                $user->id,
                $request->current_password,
                $request->new_password
            );

            return redirect()->route('user.change-password')->with('success', 'Password changed successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Format user data for responses
     */
    protected function formatUserData($user)
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->fname . ' ' . $user->lname,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address
        ];
    }
}
