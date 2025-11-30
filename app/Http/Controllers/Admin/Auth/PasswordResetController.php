<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendPasswordReset;
use App\Http\Requests\Admin\VerifyPasswordReset;
use App\Http\Requests\Admin\PasswordReset;
use App\Services\AdminService;
use App\Services\MessageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function __construct(
        private AdminService $adminService,
        private MessageService $messageService
    ) {}

    /**
     * Show forgot password form
     */
    public function showForgotPassword()
    {
        return view('admin.auth.forgot-password');
    }

    /**
     * Send password reset link
     */
    public function sendPasswordReset(SendPasswordReset $request)
    {
        $admin = $this->adminService->findAdminByEmail($request->email);

        if (!$admin) {
            return redirect()->back()->with('error', 'Admin not found with this email');
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $this->messageService->sendPasswordResetLink($admin->email, $token);

        return redirect()->back()->with('success', 'Password reset link sent to your email');
    }

    /**
     * Show reset password form
     */
    public function showResetPassword($token)
    {
        return view('admin.auth.forgot-password', ['token' => $token]);
    }

    /**
     * Verify OTP and reset password
     */
    public function verifyPasswordReset(VerifyPasswordReset $request)
    {
        try {
            $result = $this->adminService->verifyOtp($request->email, $request->otp);

            if ($result['success']) {
                return redirect()->route('admin.reset-password', ['token' => $request->token])
                    ->with('success', 'OTP verified successfully');
            }

            return redirect()->back()->with('error', $result['message']);
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
            ->first();

        if (!$tokenRecord) {
            return redirect()->back()->with('error', 'Invalid reset token');
        }

        if (!Hash::check($request->token, $tokenRecord->token)) {
            return redirect()->back()->with('error', 'Invalid reset token');
        }

        if (now()->diffInMinutes($tokenRecord->created_at) > 60) {
            return redirect()->back()->with('error', 'Reset token has expired');
        }

        $admin = $this->adminService->findAdminByEmail($request->email);

        if (!$admin) {
            return redirect()->back()->with('error', 'Admin not found');
        }

        $this->adminService->updatePassword($admin->id, $request->password);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('admin.login')->with('success', 'Password reset successfully');
    }
}
