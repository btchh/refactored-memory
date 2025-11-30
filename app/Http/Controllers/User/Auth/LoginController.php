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
}
