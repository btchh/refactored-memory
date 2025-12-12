<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Login;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function __construct(
        private AuthService $authService
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

        return view('user.auth.login');
    }

    /**
     * Handle user login
     */
    public function login(Login $request)
    {
        Log::info('User login attempt', ['username' => $request->username]);
        
        $remember = $request->boolean('remember', false);

        $result = $this->authService->loginUser($request->username, $request->password, $remember);

        Log::info('User login result', ['success' => $result['success'], 'message' => $result['message']]);

        if ($result['success']) {
            Log::info('User redirecting to dashboard');
            return redirect()->route('user.dashboard')->with('success', $result['message']);
        }

        Log::info('User login failed, redirecting back');
        
        // Check if account is restricted (archived or disabled)
        if (isset($result['reason']) && in_array($result['reason'], ['archived', 'disabled'])) {
            return redirect()->back()->withInput()->with([
                'error' => $result['message'],
                'account_restricted' => true
            ]);
        }
        
        // Handle spam block - show error and stay on page
        if (isset($result['action']) && $result['action'] === 'block') {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }
        
        // Handle too many attempts - redirect to forgot password
        if (isset($result['action']) && $result['action'] === 'redirect') {
            return redirect()->route('user.forgot-password')
                ->with('warning', $result['message']);
        }
        
        // Handle warning state - show forgot password suggestion
        if (isset($result['attempt_warning'])) {
            $warning = $result['attempt_warning'];
            
            if ($warning['action'] === 'redirect') {
                return redirect()->route('user.forgot-password')
                    ->with('warning', $warning['message']);
            }
            
            if ($warning['action'] === 'warning') {
                return redirect()->back()->withInput()->with([
                    'error' => $result['message'],
                    'warning' => $warning['message'],
                ]);
            }
        }
        
        return redirect()->back()->withInput()->with('error', $result['message']);
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        // Logout from auth guard first
        $this->authService->logoutUser();
        
        // Flush all session data completely
        $request->session()->flush();
        
        // Invalidate the session (destroys the session)
        $request->session()->invalidate();
        
        // Regenerate CSRF token for security
        $request->session()->regenerateToken();
        
        // Clear any cookies that might store credentials
        $response = redirect()->route('user.login')
            ->with('success', 'Logged out successfully');
        
        // Set aggressive cache headers to prevent back button access
        return $response
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate, private')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT')
            ->withCookie(cookie()->forget('remember_web_' . sha1(static::class)));
    }
}
