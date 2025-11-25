<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\AuthService;
use App\Services\UserService;
use App\Services\GeocodeService;
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
        private GeocodeService $geocodeService,
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
            return redirect()->route('user.dashboard')->with('success', $result['message']);
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
            \Log::error('Registration failed: ' . $e->getMessage());
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

    // public function storeBooking(Request $request)
    //     {
    //         $request->validate([
    //             'date' => 'required|date',
    //             'time' => 'required'
    //         ]);

    //         // Example save logic
    //         \App\Models\Booking::create([
    //             'user_id' => auth()->id(),
    //             'date' => $request->date,
    //             'time' => $request->time,
    //         ]);

    //         return redirect()->route('user.booking')->with('success', 'Booking submitted successfully!');
    //     }

    //     // Dashboard / Status method
    // public function showStatus()
    // {
    //     $bookings = Booking::where('user_id', auth()->id())->latest()->get();

    //     return view('user.dashboard', compact('bookings'));
    // }


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
     * Show the booking form
     */
    public function showBooking()
    {
        return view('user.booking'); 
    }

    /**
     * Submit booking
     */
    public function submitBooking(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required'
        ]);

        Booking::create([
            'user_id' => auth()->id(),
            'date' => $request->date,
            'time' => $request->time,
        ]);

        return redirect()->route('user.booking')->with('success', 'Booking submitted successfully!');
    }

    /**
     * Show booking status
     */
    public function showStatus()
    {
        $bookings = Booking::where('user_id', auth()->id())->latest()->get();
        return view('user.status', compact('bookings'));
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
        // Update basic fields
        $this->userService->updateUser(
            $user->id,
            $request->only(['username', 'fname', 'lname', 'email', 'phone', 'address'])
        );

        return redirect()->route('user.profile')->with('success', 'Profile updated successfully');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }
}


    public function shopLocation()
    {
        return view('user.shop-location');
    }

    public function history()
    {
        $bookings = Booking::where('user_id', auth()->id())->latest()->get();
        return view('user.history', compact('bookings'));
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
     * Show admin location tracking page
     */
    public function showTrackAdmin()
    {
        return view('user.track-admin');
    }

    /**
     * Get admin location data with ETA
     */
    public function getAdminLocation(Request $request)
    {
        $user = Auth::guard('web')->user();
        
        // Geocode user's address if needed
        if ($user && (!$user->latitude || !$user->longitude) && $user->address) {
            $userCoords = $this->geocodeService->geocodeAddress($user->address);
            if ($userCoords) {
                $user->update([
                    'latitude' => $userCoords['latitude'],
                    'longitude' => $userCoords['longitude']
                ]);
            }
        }
        
        // Get all admins with addresses
        $admins = \App\Models\Admin::select('id', 'fname', 'lname', 'phone', 'address', 'latitude', 'longitude', 'location_updated_at')
            ->whereNotNull('address')
            ->get()
            ->map(function ($admin) use ($user) {
                // If coordinates don't exist, geocode the address
                if (empty($admin->latitude) || empty($admin->longitude)) {
                    $coordinates = $this->geocodeService->geocodeAddress($admin->address);
                    
                    if ($coordinates) {
                        // Update admin with geocoded coordinates
                        $admin->update([
                            'latitude' => $coordinates['latitude'],
                            'longitude' => $coordinates['longitude'],
                            'location_updated_at' => now()
                        ]);
                        
                        $admin->latitude = $coordinates['latitude'];
                        $admin->longitude = $coordinates['longitude'];
                    } else {
                        return null; // Skip admins with invalid addresses
                    }
                }

                $adminData = [
                    'id' => $admin->id,
                    'name' => $admin->fname . ' ' . $admin->lname,
                    'phone' => $admin->phone,
                    'address' => $admin->address,
                    'latitude' => (float) $admin->latitude,
                    'longitude' => (float) $admin->longitude,
                    'updated_at' => $admin->location_updated_at ? $admin->location_updated_at->diffForHumans() : 'Never'
                ];

                // Calculate distance and ETA if user has coordinates
                if ($user && $user->latitude && $user->longitude) {
                    $straightDistance = $this->calculateDistance(
                        $user->latitude,
                        $user->longitude,
                        $admin->latitude,
                        $admin->longitude
                    );
                    
                    // Apply road factor (roads are typically 1.3-1.5x longer than straight line)
                    $roadFactor = 1.4;
                    $actualDistance = $straightDistance * $roadFactor;
                    
                    // Assuming 30 km/h average speed in city traffic
                    $averageSpeed = 30; // km/h
                    $travelTimeMinutes = ($actualDistance / $averageSpeed) * 60;
                    
                    $adminData['distance_km'] = round($actualDistance, 2);
                    $adminData['eta_minutes'] = round($travelTimeMinutes, 1);
                    $adminData['eta'] = now()->addMinutes($travelTimeMinutes)->format('h:i A');
                }

                return $adminData;
            })
            ->filter(); // Remove null entries

        return response()->json([
            'success' => true,
            'admins' => $admins->values(),
            'user_location' => $user && $user->latitude ? [
                'latitude' => (float) $user->latitude,
                'longitude' => (float) $user->longitude
            ] : null
        ]);
    }

    /**
     * Calculate distance between two points using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km
        
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return round($earthRadius * $c, 2);
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