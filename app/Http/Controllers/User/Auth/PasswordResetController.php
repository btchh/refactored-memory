<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\PasswordReset;
use App\Services\UserService;
use Illuminate\Http\Request;

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
        return view('user.auth.forgot-password');
    }

    /**
     * Send password reset OTP
     */
    public function sendPasswordResetOtp(Request $request)
    {
        // Manual validation for JSON response
        $validator = \Validator::make($request->all(), [
            'phone' => ['required', 'string', 'regex:/^(09|\+639)\d{9}$/'],
        ], [
            'phone.required' => 'Phone number is required',
            'phone.regex' => 'Phone number must be a valid Philippine mobile number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $result = $this->userService->sendPasswordResetOtp($request->phone);

            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Verify password reset OTP
     */
    public function verifyPasswordResetOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required|digits:6'
        ]);

        try {
            $result = $this->userService->verifyOtp($request->phone, $request->otp);
            
            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Reset password (handles form submission from step 3)
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^(09|\+639)\d{9}$/'],
            'otp' => 'required|digits:6',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            $result = $this->userService->resetPassword(
                $request->phone,
                $request->otp,
                $request->password
            );

            if ($result['success']) {
                return redirect()->route('user.login')->with('success', $result['message']);
            }

            return redirect()->back()->with('error', $result['message']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
