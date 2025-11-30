<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfile;
use App\Http\Requests\User\ChangePassword;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    /**
     * Show user profile
     */
    public function showProfile()
    {
        return view('user.profile.index');
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

    /**
     * Show change password form
     */
    public function showChangePassword()
    {
        return view('user.auth.change-password');
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
}
