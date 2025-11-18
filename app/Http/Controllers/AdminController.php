<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\AuthService;
use App\Services\AdminService;
use App\Http\Requests\Admin\Login;
use App\Http\Requests\Admin\SendPasswordReset;
use App\Http\Requests\Admin\PasswordReset;
use App\Http\Requests\Admin\UpdateProfile;
use App\Http\Requests\Admin\ChangePassword;
use App\Http\Requests\Admin\VerifyPasswordReset;
use App\Http\Requests\Admin\CreateAdmin;

class AdminController extends Controller
{
    public function __construct(
        private AdminService $adminService,
        private AuthService $authService,
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
            return redirect()->route('users.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Handle admin login
     */
    public function login(Login $request)
    {
        \Log::info('Login attempt', ['admin_name' => $request->admin_name]);
        
        $remember = $request->boolean('remember', false);
        $loginField = $request->admin_name;

        $result = $this->authService->loginAdmin($loginField, $request->password, $remember);

        \Log::info('Login result', ['success' => $result['success'], 'message' => $result['message']]);

        if ($result['success']) {
            \Log::info('Redirecting to dashboard');
            return redirect()->route('admin.dashboard')->with('success', $result['message']);
        }

        \Log::info('Login failed, redirecting back');
        return redirect()->back()->withInput()->with('error', $result['message']);
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        $this->authService->logoutAdmin();
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
        return view('admin.forgot-password');
    }

    /**
     * Send password reset link
     */
    public function sendPasswordReset(SendPasswordReset $request)
    {
        $admin = $this->adminService->findAdminByEmail($request->email);

        if ($admin) {
            $token = \Illuminate\Support\Facades\Password::createToken($admin);

            try {
                \Illuminate\Support\Facades\Log::info('Password reset email sent to admin: ' . $admin->email);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send password reset email: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'If this email exists, you will receive a password reset link.');
    }

    /**
     * Show reset password form
     */
    public function showResetPassword($token)
    {
        return view('admin.reset-password', ['token' => $token]);
    }

    /**
     * Verify OTP and reset password
     */
    public function verifyPasswordReset(VerifyPasswordReset $request)
    {
        try {
            $admin = $this->adminService->findAdmin($request->phone);

            if (!$admin) {
                return redirect()->back()->with('error', 'Admin not found.');
            }

            // Verify OTP (implementation depends on your OTP service)
            // This is a placeholder - implement actual OTP verification
            return redirect()->back()->with('success', 'OTP verified successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reset password with token
     */
    public function resetPassword(PasswordReset $request)
    {
        $tokenRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        $tokenValid = $tokenRecord && now()->subMinutes(60)->lessThan($tokenRecord->created_at);

        if (!$tokenValid) {
            return redirect()->back()->with('error', 'This password reset token is invalid or has expired.');
        }

        $admin = $this->adminService->findAdminByEmail($request->email);

        if (!$admin) {
            return redirect()->back()->with('error', 'Admin not found.');
        }

        $this->adminService->updateAdmin($admin->id, [
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return redirect()->route('admin.login')->with('success', 'Password has been reset successfully. You can now login with your new password.');
    }

    /**
     * Show admin dashboard
     */
    public function showDashboard()
    {
        return view('admin.dashboard');
    }

    /**
     * Show admin profile
     */
    public function showProfile()
    {
        return view('admin.profile');
    }

    /**
     * Show change password form
     */
    public function showChangePassword()
    {
        return view('admin.change-password');
    }

    /**
     * Show create admin form
     */
    public function showCreateAdmin()
    {
        return view('admin.create-admin');
    }

    /**
     * Update admin profile
     */
    public function updateProfile(UpdateProfile $request)
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return redirect()->route('admin.login')->with('error', 'Unauthorized');
        }

        $this->adminService->updateAdmin(
            $admin->id,
            $request->only(['admin_name', 'fname', 'lname', 'email', 'phone', 'address'])
        );

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully');
    }

    /**
     * Change admin password
     */
    public function changePassword(ChangePassword $request)
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return redirect()->route('admin.login')->with('error', 'Unauthorized');
        }

        try {
            $this->adminService->changePass(
                $admin->id,
                $request->current_password,
                $request->new_password
            );

            return redirect()->route('admin.change-password')->with('success', 'Password changed successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Create new admin
     */
    public function createAdmin(CreateAdmin $request)
    {
        $currentAdmin = Auth::guard('admin')->user();

        if (!$currentAdmin) {
            return redirect()->route('admin.login')->with('error', 'Unauthorized');
        }

        try {
            $this->adminService->createAdmin($request->validated());

            return redirect()->route('admin.create-admin')->with('success', 'Admin created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Format admin data for responses
     */
    protected function formatAdminData($admin)
    {
        return [
            'id' => $admin->id,
            'admin_name' => $admin->admin_name,
            'name' => $admin->fname . ' ' . $admin->lname,
            'email' => $admin->email,
            'phone' => $admin->phone,
            'address' => $admin->address
        ];
    }
}
