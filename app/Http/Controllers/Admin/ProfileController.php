<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProfile;
use App\Http\Requests\Admin\ChangePassword;
use App\Services\AdminService;
use App\Services\GeocodeService;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(
        private AdminService $adminService,
        private GeocodeService $geocodeService
    ) {}

    /**
     * Show admin profile
     */
    public function showProfile()
    {
        return view('admin.profile.index');
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

        $data = $request->validated();

        // Geocode personal address if changed
        if (isset($data['address']) && $data['address'] !== $admin->address) {
            try {
                $coordinates = $this->geocodeService->geocodeAddress($data['address']);
                if ($coordinates) {
                    $data['latitude'] = $coordinates['latitude'];
                    $data['longitude'] = $coordinates['longitude'];
                    $data['location_updated_at'] = now();
                }
            } catch (\Exception $e) {
                \Log::error('Failed to geocode admin address: ' . $e->getMessage());
            }
        }

        // Geocode branch address if provided and changed
        if (isset($data['branch_address']) && $data['branch_address'] !== $admin->branch_address) {
            try {
                $coordinates = $this->geocodeService->geocodeAddress($data['branch_address']);
                if ($coordinates) {
                    $data['branch_latitude'] = $coordinates['latitude'];
                    $data['branch_longitude'] = $coordinates['longitude'];
                }
            } catch (\Exception $e) {
                \Log::error('Failed to geocode branch address: ' . $e->getMessage());
            }
        }

        $this->adminService->updateAdmin($admin->id, $data);

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully');
    }

    /**
     * Show change password form
     */
    public function showChangePassword()
    {
        return view('admin.auth.change-password');
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
            $this->adminService->changePassword(
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
