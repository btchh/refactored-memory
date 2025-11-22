<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\AuthService;
use App\Services\UserService;
use App\Services\GeocodeService;
use App\Services\LocationService;
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
    use \App\Traits\Responses;

    public function __construct(
        private UserService $userService,
        private AuthService $authService,
        private GeocodeService $geocodeService,
        private LocationService $locationService,
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
        $remember = $request->boolean('remember', false);

        $result = $this->authService->loginUser($request->username, $request->password, $remember);

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
            return $this->errorResponse(
                $validator->errors()->first(),
                $validator->errors()->toArray(),
                422
            );
        }

        try {
            $result = $this->userService->sendRegistrationOtp($request->phone, $request->email);

            if ($result['success']) {
                return $this->successResponse($result['message']);
            }
            
            return $this->errorResponse($result['message'], [], 400);
        } catch (\Exception $e) {
            \Log::error('Failed to send registration OTP: ' . $e->getMessage());
            return $this->errorResponse('Failed to send OTP', [], 500);
        }
    }

    /**
     * Verify OTP before registration
     */
    public function verifyRegistrationOtp(Request $request)
    {
        $validator = \Validator::make($request->all(), [
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
            $result = $this->userService->verifyOtp($request->phone, $request->otp);
            
            // If OTP verification succeeds, mark it as verified in cache (valid for 10 minutes)
            if ($result['success']) {
                \Cache::put('registration_otp_verified_' . $request->phone, true, 600);
                return $this->successResponse($result['message']);
            }
            
            return $this->errorResponse($result['message'], [], 400);
        } catch (\Exception $e) {
            \Log::error('Failed to verify registration OTP: ' . $e->getMessage());
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
            $verified = \Cache::get('registration_otp_verified_' . $request->phone);
            
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
            $result = $this->userService->createUser($request->validated());

            // Clear the verification cache after successful registration
            \Cache::forget('registration_otp_verified_' . $request->phone);

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
            \Log::error('Registration failed: ' . $e->getMessage());
            
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
     * Verify password reset OTP and redirect to password reset page
     */
    public function verifyPasswordResetOtp(VerifyPasswordResetOtp $request)
    {
        try {
            $otpResult = $this->userService->verifyOtp($request->phone, $request->otp);

            if (!$otpResult['success']) {
                return redirect()->back()->with('error', 'Invalid or expired OTP');
            }

            // Mark OTP as verified for this phone number (valid for 10 minutes)
            \Cache::put('otp_verified_' . $request->phone, true, 600);

            return redirect()->route('user.reset-password', ['phone' => $request->phone])
                ->with('success', 'OTP verified successfully. Please enter your new password.');
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
            // Check if OTP was verified for this phone number
            $verified = \Cache::get('otp_verified_' . $request->phone);
            
            if (!$verified) {
                return redirect()->route('user.forgot-password')
                    ->with('error', 'Please verify OTP first before resetting password.');
            }

            $this->userService->completePassReset($request->phone, $request->password);

            // Clear the verification cache after successful password reset
            \Cache::forget('otp_verified_' . $request->phone);

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
     * Show admin location tracking page
     */
    public function showTrackAdmin()
    {
        return view('user.track-admin');
    }

    /**
     * Get admin location data with ETA
     * 
     * Performance notes:
     * - Uses select() to fetch only required columns
     * - Geocoding only occurs when force_geocode=true and coordinates are missing
     * - Individual admin updates are intentional (geocoding is expensive and should be cached)
     * - Query logging is enabled in development (see AppServiceProvider)
     */
    public function getAdminLocation(Request $request)
    {
        $user = Auth::guard('web')->user();
        
        if (!$user) {
            return $this->errorResponse('User not authenticated', [], 401);
        }
        
        $forceGeocode = $request->boolean('force_geocode', false);
        
        // Only geocode user's address if forced or coordinates are missing
        if ((!$user->latitude || !$user->longitude) && $user->address && $forceGeocode) {
            \Log::info('Geocoding user address', ['user_id' => $user->id, 'address' => $user->address]);
            $userCoords = $this->geocodeService->geocodeAddress($user->address);
            
            if ($userCoords && isset($userCoords['latitude']) && isset($userCoords['longitude'])) {
                // Validate coordinates before updating
                if (is_numeric($userCoords['latitude']) && is_numeric($userCoords['longitude'])) {
                    $user->update([
                        'latitude' => $userCoords['latitude'],
                        'longitude' => $userCoords['longitude']
                    ]);
                    \Log::info('User location geocoded successfully', ['user_id' => $user->id]);
                } else {
                    \Log::warning('Geocoding returned invalid coordinates for user', [
                        'user_id' => $user->id,
                        'coordinates' => $userCoords,
                        'reason' => 'non_numeric'
                    ]);
                }
            } else {
                // Geocoding failed - log but continue without user coordinates
                \Log::warning('Geocoding failed for user address', [
                    'user_id' => $user->id,
                    'address' => $user->address,
                    'reason' => 'geocoding_returned_null'
                ]);
            }
        }
        
        // Get all admins with addresses and existing coordinates
        // Performance optimization: Using select() to only fetch needed columns
        // This reduces memory usage and network transfer when dealing with many admins
        // Note: No eager loading needed as Admin model has no relationships accessed here
        $admins = \App\Models\Admin::select('id', 'fname', 'lname', 'phone', 'address', 'latitude', 'longitude', 'location_updated_at')
            ->whereNotNull('address')
            ->get()
            ->map(function ($admin) use ($user, $forceGeocode) {
                // Skip admins without address
                if (empty($admin->address)) {
                    return null;
                }
                
                // Only geocode if forced and coordinates don't exist
                if ((empty($admin->latitude) || empty($admin->longitude)) && $forceGeocode) {
                    \Log::info('Geocoding admin address', ['admin_id' => $admin->id, 'address' => $admin->address]);
                    $coordinates = $this->geocodeService->geocodeAddress($admin->address);
                    
                    if ($coordinates) {
                        // Validate coordinates before updating
                        if (!isset($coordinates['latitude']) || !isset($coordinates['longitude'])) {
                            \Log::warning('Geocoding returned incomplete coordinates for admin', [
                                'admin_id' => $admin->id,
                                'coordinates' => $coordinates,
                                'reason' => 'missing_lat_or_lon'
                            ]);
                            return null;
                        }
                        
                        if (!is_numeric($coordinates['latitude']) || !is_numeric($coordinates['longitude'])) {
                            \Log::warning('Geocoding returned non-numeric coordinates for admin', [
                                'admin_id' => $admin->id,
                                'coordinates' => $coordinates,
                                'reason' => 'non_numeric'
                            ]);
                            return null;
                        }
                        
                        // Update admin with geocoded coordinates
                        $admin->update([
                            'latitude' => $coordinates['latitude'],
                            'longitude' => $coordinates['longitude'],
                            'location_updated_at' => now()
                        ]);
                        
                        $admin->latitude = $coordinates['latitude'];
                        $admin->longitude = $coordinates['longitude'];
                        \Log::info('Admin location geocoded successfully', ['admin_id' => $admin->id]);
                    } else {
                        // Geocoding failed - log and skip this admin
                        \Log::warning('Geocoding failed for admin address', [
                            'admin_id' => $admin->id,
                            'address' => $admin->address,
                            'reason' => 'geocoding_returned_null'
                        ]);
                        return null; // Skip admins with invalid addresses
                    }
                }

                // Skip admins without valid coordinates
                if (empty($admin->latitude) || empty($admin->longitude) || 
                    !is_numeric($admin->latitude) || !is_numeric($admin->longitude)) {
                    return null;
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

                // Calculate distance and ETA only if user has valid coordinates
                if ($user && $user->latitude && $user->longitude && 
                    is_numeric($user->latitude) && is_numeric($user->longitude)) {
                    try {
                        $route = $this->locationService->calculateRoute(
                            (float) $user->latitude,
                            (float) $user->longitude,
                            (float) $admin->latitude,
                            (float) $admin->longitude
                        );
                        
                        $adminData['distance_km'] = $route['distance_km'];
                        $adminData['eta_minutes'] = $route['time_minutes'];
                        $adminData['eta'] = $route['eta'];
                        $adminData['current_time'] = $route['current_time'];
                    } catch (\InvalidArgumentException $e) {
                        // Log invalid coordinate error but continue without route data
                        \Log::warning('Route calculation failed for admin: Invalid coordinates', [
                            'user_id' => $user->id,
                            'admin_id' => $admin->id,
                            'error' => $e->getMessage(),
                            'reason' => 'invalid_argument'
                        ]);
                    } catch (\Exception $e) {
                        // Log unexpected error but continue without route data
                        \Log::error('Route calculation failed for admin: Unexpected error', [
                            'user_id' => $user->id,
                            'admin_id' => $admin->id,
                            'error' => $e->getMessage(),
                            'reason' => 'exception'
                        ]);
                    }
                }

                return $adminData;
            })
            ->filter(); // Remove null entries

        return $this->successResponse('Admin locations retrieved successfully', [
            'admins' => $admins->values(),
            'user_location' => $user && $user->latitude && $user->longitude && 
                               is_numeric($user->latitude) && is_numeric($user->longitude) ? [
                'latitude' => (float) $user->latitude,
                'longitude' => (float) $user->longitude
            ] : null
        ]);
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
