<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Login;
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
        Log::info('Login attempt', ['admin_name' => $request->admin_name]);

        $remember = $request->boolean('remember', false);

        $result = $this->authService->loginAdmin($request->admin_name, $request->password, $remember);

        Log::info('Login result', ['success' => $result['success'], 'message' => $result['message']]);

        if ($result['success']) {
            Log::info('Redirecting to dashboard');
            return redirect()->route('admin.dashboard')->with('success', $result['message']);
        }

        Log::info('Login failed, redirecting back');
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
}
