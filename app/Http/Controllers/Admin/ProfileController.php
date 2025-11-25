<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Admin\AdminManagementService;
use App\Services\Admin\AdminProfileService;
use App\Http\Requests\Admin\UpdateProfile;
use App\Http\Requests\Admin\ChangePassword;

class ProfileController extends Controller
{
    public function __construct(
        private AdminManagementService $adminManagementService,
        private AdminProfileService $adminProfileService
    ) {}

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
        return view('admin.profile.profile');
    }

    /**
     * Show change password form
     */
    public function showChangePassword()
    {
        return view('admin.profile.change-password');
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

        try {
            $this->adminManagementService->updateAdmin(
                $admin->id,
                $request->only(['admin_name', 'fname', 'lname', 'email', 'phone', 'address'])
            );

            return redirect()->route('admin.profile')->with('success', 'Profile updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
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
            $this->adminProfileService->changePassword(
                $admin->id,
                $request->current_password,
                $request->new_password
            );

            return redirect()->route('admin.change-password')->with('success', 'Password changed successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
