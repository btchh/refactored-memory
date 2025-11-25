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
            return redirect()->route('user.dashboard');
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
     * Send OTP for admin creation
     */
    public function sendAdminOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:admins,email',
            'phone' => 'required|unique:admins,phone'
        ]);

        try {
            // Generate OTP
            $otp = rand(100000, 999999);
            
            // Store OTP in cache for 10 minutes
            \Cache::put('admin_otp_' . $request->phone, $otp, 600);
            
            // Send OTP via SMS (using your SMS service)
            $smsService = app(\App\Services\SmsService::class);
            $smsService->sendOtp($request->phone, $otp);
            
            // TODO: Also send via email if needed
            
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully'
            ]);
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
    public function verifyAdminOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required|digits:6'
        ]);

        $cachedOtp = \Cache::get('admin_otp_' . $request->phone);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP'
            ], 400);
        }

        // Mark as verified
        \Cache::put('admin_otp_verified_' . $request->phone, true, 600);
        \Cache::forget('admin_otp_' . $request->phone);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully'
        ]);
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

        // Check if OTP was verified
        $verified = \Cache::get('admin_otp_verified_' . $request->phone);
        if (!$verified) {
            return redirect()->back()->withInput()->with('error', 'Please verify OTP first');
        }

        try {
            $this->adminService->createAdmin($request->validated());
            
            // Clear verification cache
            \Cache::forget('admin_otp_verified_' . $request->phone);

            return redirect()->route('admin.create-admin')->with('success', 'Admin created successfully');
        } catch (\Exception $e) {
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
            return response()->json(['success' => false, 'message' => 'Admin or User not found'], 404);
        }

        // Geocode addresses if coordinates don't exist
        $geocodeService = app(\App\Services\GeocodeService::class);
        
        if (empty($admin->latitude) || empty($admin->longitude)) {
            $adminCoords = $geocodeService->geocodeAddress($admin->address);
            if ($adminCoords) {
                $admin->update([
                    'latitude' => $adminCoords['latitude'],
                    'longitude' => $adminCoords['longitude'],
                    'location_updated_at' => now()
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Could not geocode admin address'], 400);
            }
        }

        if (empty($user->latitude) || empty($user->longitude)) {
            $userCoords = $geocodeService->geocodeAddress($user->address);
            if ($userCoords) {
                $user->update([
                    'latitude' => $userCoords['latitude'],
                    'longitude' => $userCoords['longitude']
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Could not geocode user address'], 400);
            }
        }

        // Get route from Geoapify
        $routeData = $this->getRouteFromGeoapify(
            $admin->latitude,
            $admin->longitude,
            $user->latitude,
            $user->longitude
        );

        if (!$routeData) {
            return response()->json(['success' => false, 'message' => 'Could not calculate route'], 400);
        }

        return response()->json([
            'success' => true,
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
                    
                    return [
                        'distance' => $properties['distance'], // in meters
                        'time' => $properties['time'], // in seconds
                        'geometry' => $feature['geometry'],
                        'distance_km' => round($properties['distance'] / 1000, 2),
                        'time_minutes' => round($properties['time'] / 60, 1),
                        'eta' => now()->addSeconds($properties['time'])->format('h:i A'),
                        'method' => 'routing'
                    ];
                }
            }

            // Fallback to straight-line distance calculation
            \Log::warning('Routing API failed, using distance calculation fallback');
            return $this->calculateStraightLineRoute($fromLat, $fromLon, $toLat, $toLon);
            
        } catch (\Exception $e) {
            \Log::error('Routing exception: ' . $e->getMessage());
            // Fallback to straight-line distance
            return $this->calculateStraightLineRoute($fromLat, $fromLon, $toLat, $toLon);
        }
    }

    /**
     * Calculate straight-line distance and estimated time
     */
    private function calculateStraightLineRoute($fromLat, $fromLon, $toLat, $toLon)
    {
        // Haversine formula for distance
        $earthRadius = 6371000; // meters
        
        $latFrom = deg2rad($fromLat);
        $lonFrom = deg2rad($fromLon);
        $latTo = deg2rad($toLat);
        $lonTo = deg2rad($toLon);
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        $distance = $earthRadius * $c; // in meters
        
        // Apply road factor (roads are typically 1.3-1.5x longer than straight line)
        $roadFactor = 1.4;
        $actualDistance = $distance * $roadFactor;
        
        // Estimate time assuming average speed of 30 km/h in city traffic
        $averageSpeed = 30; // km/h (realistic city driving with traffic)
        $time = ($actualDistance / 1000) / $averageSpeed * 3600; // in seconds
        
        // Create simple straight line geometry
        $geometry = [
            'type' => 'LineString',
            'coordinates' => [
                [$fromLon, $fromLat],
                [$toLon, $toLat]
            ]
        ];
        
        return [
            'distance' => $actualDistance,
            'time' => $time,
            'geometry' => $geometry,
            'distance_km' => round($actualDistance / 1000, 2),
            'time_minutes' => round($time / 60, 1),
            'eta' => now()->addSeconds($time)->format('h:i A'),
            'method' => 'estimated'
        ];
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
