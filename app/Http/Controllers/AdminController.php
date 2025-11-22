<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\AuthService;
use App\Services\AdminService;
use App\Services\LocationService;
use App\Http\Requests\Admin\Login;
use App\Http\Requests\Admin\SendPasswordReset;
use App\Http\Requests\Admin\PasswordReset;
use App\Http\Requests\Admin\UpdateProfile;
use App\Http\Requests\Admin\ChangePassword;
use App\Http\Requests\Admin\VerifyPasswordReset;
use App\Http\Requests\Admin\CreateAdmin;

class AdminController extends Controller
{
    use \App\Traits\Responses;

    public function __construct(
        private AdminService $adminService,
        private AuthService $authService,
        private LocationService $locationService,
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
            return redirect()->route('user.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Handle admin login
     */
    public function login(Login $request)
    {
        $remember = $request->boolean('remember', false);
        $loginField = $request->admin_name;

        $result = $this->authService->loginAdmin($loginField, $request->password, $remember);

        if ($result['success']) {
            return redirect()->route('admin.dashboard')->with('success', $result['message']);
        }

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
            'password' => $request->password
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
     * Send OTP for admin creation
     */
    public function sendAdminOtp(Request $request)
    {
        $validator = \Validator::make($request->all(), [
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
            // Generate OTP
            $otp = rand(100000, 999999);
            
            // Store OTP in cache for 10 minutes
            \Cache::put('admin_otp_' . $request->phone, $otp, 600);
            
            // Send OTP via SMS (using your SMS service)
            $smsService = app(\App\Services\SmsService::class);
            $smsService->sendOtp($request->phone, $otp);
            
            // TODO: Also send via email if needed
            
            return $this->successResponse('OTP sent successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to send admin OTP: ' . $e->getMessage());
            return $this->errorResponse('Failed to send OTP', [], 500);
        }
    }

    /**
     * Verify OTP for admin creation
     */
    public function verifyAdminOtp(Request $request)
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

        $cachedOtp = \Cache::get('admin_otp_' . $request->phone);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return $this->errorResponse('Invalid or expired OTP', [], 400);
        }

        // Mark as verified
        \Cache::put('admin_otp_verified_' . $request->phone, true, 600);
        \Cache::forget('admin_otp_' . $request->phone);

        return $this->successResponse('OTP verified successfully');
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
        $verified = \Cache::get('admin_otp_verified_' . $request->phone);
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
            $this->adminService->createAdmin($request->validated());
            
            // Clear verification cache
            \Cache::forget('admin_otp_verified_' . $request->phone);

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

    /**
     * Show route to user page
     */
    public function showRouteToUser()
    {
        return view('admin.route-to-user');
    }

    /**
     * Get route from admin to user with ETA
     */
    public function getRouteToUser($userId)
    {
        $admin = Auth::guard('admin')->user();
        $user = \App\Models\User::find($userId);

        if (!$admin || !$user) {
            return $this->errorResponse('Admin or User not found', [], 404);
        }

        // Geocode addresses if coordinates don't exist (only when actively requesting route)
        $geocodeService = app(\App\Services\GeocodeService::class);
        
        if (empty($admin->latitude) || empty($admin->longitude)) {
            if (!$admin->address) {
                \Log::warning('Route calculation failed: Admin address not set', ['admin_id' => $admin->id]);
                return $this->errorResponse('Admin address is not set. Please update your profile with a valid address.', [], 400);
            }
            
            \Log::info('Geocoding admin address for route calculation', ['admin_id' => $admin->id, 'address' => $admin->address]);
            $adminCoords = $geocodeService->geocodeAddress($admin->address);
            
            if ($adminCoords && isset($adminCoords['latitude']) && isset($adminCoords['longitude'])) {
                // Validate coordinates before updating
                if (is_numeric($adminCoords['latitude']) && is_numeric($adminCoords['longitude'])) {
                    $admin->update([
                        'latitude' => $adminCoords['latitude'],
                        'longitude' => $adminCoords['longitude'],
                        'location_updated_at' => now()
                    ]);
                    \Log::info('Admin location geocoded successfully', ['admin_id' => $admin->id]);
                } else {
                    \Log::warning('Geocoding returned invalid coordinates', [
                        'admin_id' => $admin->id,
                        'coordinates' => $adminCoords,
                        'reason' => 'non_numeric'
                    ]);
                    return $this->errorResponse('Unable to determine admin location. The geocoding service returned invalid coordinates.', [], 400);
                }
            } else {
                // Geocoding failed - log and return graceful error
                \Log::warning('Geocoding failed for admin address', [
                    'admin_id' => $admin->id,
                    'address' => $admin->address,
                    'reason' => 'geocoding_returned_null'
                ]);
                return $this->errorResponse('Unable to geocode admin address. Please verify the address is correct and try again.', [], 400);
            }
        }

        if (empty($user->latitude) || empty($user->longitude)) {
            if (!$user->address) {
                \Log::warning('Route calculation failed: User address not set', ['user_id' => $user->id]);
                return $this->errorResponse('User address is not set. The user needs to update their profile with a valid address.', [], 400);
            }
            
            \Log::info('Geocoding user address for route calculation', ['user_id' => $user->id, 'address' => $user->address]);
            $userCoords = $geocodeService->geocodeAddress($user->address);
            
            if ($userCoords && isset($userCoords['latitude']) && isset($userCoords['longitude'])) {
                // Validate coordinates before updating
                if (is_numeric($userCoords['latitude']) && is_numeric($userCoords['longitude'])) {
                    $user->update([
                        'latitude' => $userCoords['latitude'],
                        'longitude' => $userCoords['longitude']
                    ]);
                    \Log::info('User location geocoded successfully', ['user_id' => $user->id]);
                } else {
                    \Log::warning('Geocoding returned invalid coordinates', [
                        'user_id' => $user->id,
                        'coordinates' => $userCoords,
                        'reason' => 'non_numeric'
                    ]);
                    return $this->errorResponse('Unable to determine user location. The geocoding service returned invalid coordinates.', [], 400);
                }
            } else {
                // Geocoding failed - log and return graceful error
                \Log::warning('Geocoding failed for user address', [
                    'user_id' => $user->id,
                    'address' => $user->address,
                    'reason' => 'geocoding_returned_null'
                ]);
                return $this->errorResponse('Unable to geocode user address. Please verify the address is correct and try again.', [], 400);
            }
        }

        // Verify both coordinates exist and are valid before attempting route calculation
        if (empty($admin->latitude) || empty($admin->longitude) || empty($user->latitude) || empty($user->longitude) ||
            !is_numeric($admin->latitude) || !is_numeric($admin->longitude) || 
            !is_numeric($user->latitude) || !is_numeric($user->longitude)) {
            \Log::error('Route calculation failed: Invalid coordinates after geocoding', [
                'admin_id' => $admin->id,
                'user_id' => $user->id,
                'admin_coords' => ['lat' => $admin->latitude, 'lon' => $admin->longitude],
                'user_coords' => ['lat' => $user->latitude, 'lon' => $user->longitude]
            ]);
            return $this->errorResponse('Cannot calculate route: Location coordinates are missing or invalid after geocoding attempt.', [], 400);
        }

        // Get route from Geoapify
        try {
            $routeData = $this->getRouteFromGeoapify(
                (float) $admin->latitude,
                (float) $admin->longitude,
                (float) $user->latitude,
                (float) $user->longitude
            );

            if (!$routeData) {
                \Log::error('Route calculation returned no data', [
                    'admin_id' => $admin->id,
                    'user_id' => $user->id
                ]);
                return $this->errorResponse('Could not calculate route. Both routing API and fallback calculation failed.', [], 400);
            }

            return $this->successResponse('Route calculated successfully', [
                'admin' => [
                    'name' => $admin->fname . ' ' . $admin->lname,
                    'latitude' => (float) $admin->latitude,
                    'longitude' => (float) $admin->longitude,
                    'address' => $admin->address
                ],
                'user' => [
                    'name' => $user->fname . ' ' . $user->lname,
                    'latitude' => (float) $user->latitude,
                    'longitude' => (float) $user->longitude,
                    'address' => $user->address
                ],
                'route' => $routeData
            ]);
        } catch (\InvalidArgumentException $e) {
            \Log::error('Route calculation failed: Invalid coordinates', [
                'admin_id' => $admin->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'reason' => 'invalid_argument'
            ]);
            return $this->errorResponse('Cannot calculate route: The coordinates provided are invalid. ' . $e->getMessage(), [], 400);
        } catch (\Exception $e) {
            \Log::error('Route calculation failed: Unexpected error', [
                'admin_id' => $admin->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'reason' => 'exception'
            ]);
            return $this->errorResponse('An unexpected error occurred while calculating the route. Please try again.', [], 500);
        }
    }

    /**
     * Get route using simple distance calculation (fallback)
     */
    private function getRouteFromGeoapify($fromLat, $fromLon, $toLat, $toLon)
    {
        try {
            $apiKey = config('services.geoapify.api_key');
            
            // Try Geoapify routing first
            $response = \Illuminate\Support\Facades\Http::withOptions([
                'verify' => false,
            ])->timeout(10)->get('https://api.geoapify.com/v1/routing', [
                'waypoints' => "{$fromLat},{$fromLon}|{$toLat},{$toLon}",
                'mode' => 'drive',
                'apiKey' => $apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data['features'][0])) {
                    $feature = $data['features'][0];
                    $properties = $feature['properties'];
                    
                    // Calculate ETA using application's configured timezone
                    $currentTime = now();
                    $etaTime = $currentTime->copy()->addSeconds($properties['time']);
                    
                    return [
                        'distance' => $properties['distance'], // in meters
                        'time' => $properties['time'], // in seconds
                        'geometry' => $feature['geometry'],
                        'distance_km' => round($properties['distance'] / 1000, 2),
                        'time_minutes' => round($properties['time'] / 60, 1),
                        'eta' => $etaTime->format('h:i A'),
                        'current_time' => $currentTime->format('h:i A'),
                        'method' => 'routing'
                    ];
                }
            }

            // Fallback to LocationService for straight-line distance calculation
            \Log::warning('Routing API failed, using distance calculation fallback', [
                'reason' => 'no_features_in_response'
            ]);
            return $this->locationService->calculateRoute($fromLat, $fromLon, $toLat, $toLon);
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            \Log::error('Routing API connection failed, using fallback', [
                'error' => $e->getMessage(),
                'reason' => 'network_error'
            ]);
            return $this->locationService->calculateRoute($fromLat, $fromLon, $toLat, $toLon);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            \Log::error('Routing API request failed, using fallback', [
                'error' => $e->getMessage(),
                'status' => $e->response ? $e->response->status() : 'unknown',
                'reason' => 'api_error'
            ]);
            return $this->locationService->calculateRoute($fromLat, $fromLon, $toLat, $toLon);
        } catch (\Exception $e) {
            \Log::error('Routing exception, using fallback', [
                'error' => $e->getMessage(),
                'reason' => 'exception'
            ]);
            return $this->locationService->calculateRoute($fromLat, $fromLon, $toLat, $toLon);
        }
    }



    /**
     * Show geocoding test page (debug only)
     */
    public function testGeocode()
    {
        if (!config('app.debug')) {
            abort(404);
        }
        
        return view('admin.test-geocode');
    }

    /**
     * Perform geocoding test (debug only)
     */
    public function performTestGeocode(\Illuminate\Http\Request $request)
    {
        if (!config('app.debug')) {
            abort(404);
        }
        
        $address = $request->input('address');
        $fresh = $request->boolean('fresh', false);
        
        if (empty($address)) {
            return $this->errorResponse('Address is required', [], 400);
        }
        
        $geocodeService = app(\App\Services\GeocodeService::class);
        
        // Clear cache if fresh is requested
        if ($fresh) {
            $geocodeService->clearCache($address);
        }
        
        $result = $fresh 
            ? $geocodeService->geocodeAddressFresh($address)
            : $geocodeService->geocodeAddress($address);
        
        if ($result) {
            return $this->successResponse('Geocoding successful', [
                'result' => $result,
                'cached' => !$fresh
            ]);
        }
        
        return $this->errorResponse('Geocoding failed. Check logs for details.', [], 400);
    }

    /**
     * Get list of users for API
     */
    public function getUsersList(Request $request)
    {
        try {
            $users = \App\Models\User::select('id', 'username', 'fname', 'lname', 'phone', 'address')
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->fname . ' ' . $user->lname,
                        'phone' => $user->phone,
                        'address' => $user->address
                    ];
                });
            
            return $this->successResponse('Users retrieved successfully', [
                'users' => $users
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve users: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve users', [], 500);
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
