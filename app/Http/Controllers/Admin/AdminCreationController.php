<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateAdmin;
use App\Services\AdminService;
use Illuminate\Http\Request;

class AdminCreationController extends Controller
{
    public function __construct(
        private AdminService $adminService
    ) {}

    /**
     * Show create admin form
     */
    public function showCreateAdmin()
    {
        return view('admin.admins.create');
    }

    /**
     * Send OTP for admin creation
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:admins,email',
            'phone' => ['required', 'string', 'regex:/^(09|\+639)\d{9}$/', 'unique:admins,phone'],
        ], [
            'email.unique' => 'This email is already registered',
            'phone.unique' => 'This phone number is already registered',
            'phone.regex' => 'Phone number must be a valid Philippine mobile number',
        ]);

        try {
            $result = $this->adminService->sendOtp($request->phone, $request->email);
            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Verify OTP for admin creation
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|digits:6',
        ]);

        try {
            $result = $this->adminService->verifyOtp($request->phone, $request->otp);
            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Create new admin
     */
    public function store(CreateAdmin $request)
    {
        try {
            $result = $this->adminService->createAdmin($request->validated());
            
            return redirect()->route('admin.dashboard')
                ->with('success', 'Admin created successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create admin: ' . $e->getMessage());
        }
    }
}
