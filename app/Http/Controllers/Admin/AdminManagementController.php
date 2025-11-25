<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateAdmin;
use App\Services\Admin\AdminManagementService;
use App\Services\Auth\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminManagementController extends Controller
{
    use \App\Traits\Responses;

    public function __construct(
        private AdminManagementService $adminManagementService,
        private OtpService $otpService,
    ) {}

    /**
     * Show create admin form
     */
    public function showCreateAdmin()
    {
        return view('admin.management.create-admin');
    }

    /**
     * Send OTP for admin creation
     */
    public function sendAdminOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:admins,email',
            'phone' => 'required|unique:admins,phone'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validation failed',
                $validator->errors()->toArray(),
                422
            );
        }

        try {
            $result = $this->otpService->sendRegistrationOtp($request->phone, $request->email);

            if ($result['success']) {
                return $this->successResponse($result['message']);
            }

            return $this->errorResponse($result['message'], [], 400);
        } catch (\Exception $e) {
            Log::error('Failed to send admin OTP: ' . $e->getMessage());
            return $this->errorResponse('Failed to send OTP', [], 500);
        }
    }

    /**
     * Verify OTP for admin creation
     */
    public function verifyAdminOtp(Request $request)
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
                Cache::put('admin_otp_verified_' . $request->phone, true, 600);
                return $this->successResponse($result['message']);
            }

            return $this->errorResponse($result['message'], [], 400);
        } catch (\Exception $e) {
            Log::error('Failed to verify admin OTP: ' . $e->getMessage());
            return $this->errorResponse('Failed to verify OTP', [], 500);
        }
    }

    /**
     * Create new admin
     */
    public function createAdmin(CreateAdmin $request)
    {
        $currentAdmin = Auth::guard('admin')->user();

        if (!$currentAdmin) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            return redirect()->route('admin.login')->with('error', 'Unauthorized');
        }

        // Check if OTP was verified
        $verified = Cache::get('admin_otp_verified_' . $request->phone);
        if (!$verified) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please verify OTP first'
                ], 400);
            }
            return redirect()->back()->withInput()->with('error', 'Please verify OTP first');
        }

        try {
            $this->adminManagementService->createAdmin($request->validated());

            // Clear verification cache
            Cache::forget('admin_otp_verified_' . $request->phone);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Admin created successfully',
                    'redirect' => route('admin.create-admin')
                ]);
            }

            return redirect()->route('admin.create-admin')->with('success', 'Admin created successfully');
        } catch (\Exception $e) {
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
