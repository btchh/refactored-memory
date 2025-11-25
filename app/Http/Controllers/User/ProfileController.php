<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\User\UserManagementService;
use App\Services\User\UserProfileService;
use App\Http\Requests\User\UpdateProfile;
use App\Http\Requests\User\ChangePassword;

class ProfileController extends Controller
{
    public function __construct(
        private UserManagementService $userManagementService,
        private UserProfileService $userProfileService
    ) {}

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
        return view('user.profile.profile');
    }

    /**
     * Show change password form
     */
    public function showChangePassword()
    {
        return view('user.profile.change-password');
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
            $this->userManagementService->updateUser(
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
            $this->userProfileService->changePassword(
                $user->id,
                $request->current_password,
                $request->new_password
            );

            return redirect()->route('user.change-password')->with('success', 'Password changed successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
