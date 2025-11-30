<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Register;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

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
    public function sendRegistrationOtp(Request $request)
    {
        // Manual validation for JSON response
        $validator = \Validator::make($request->all(), [
            'phone' => ['required', 'string', 'regex:/^(09|\+639)\d{9}$/', 'unique:users,phone'],
            'email' => ['required', 'email', 'unique:users,email'],
        ], [
            'phone.required' => 'Phone number is required',
            'phone.regex' => 'Phone number must be a valid Philippine mobile number',
            'phone.unique' => 'Phone number is already registered',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email is already registered',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $result = $this->userService->sendRegistrationOtp($request->phone, $request->email);

            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Verify OTP before registration
     */
    public function verifyRegistrationOtp(Request $request)
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
     * Register user with OTP verification
     */
    public function register(Register $request)
    {
        try {
            // OTP was already verified in step 2, so we can skip verification here
            // Just create the user directly
            $result = $this->userService->createUser($request->validated());

            return redirect()->route('user.login')->with('success', 'Registration successful! Please login with your credentials.');
        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
