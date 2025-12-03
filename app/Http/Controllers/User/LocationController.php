<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Transaction;
use App\Services\GeocodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function __construct(
        private GeocodeService $geocodeService
    ) {}

    /**
     * Show route to admin page (track shop/delivery personnel)
     */
    public function showRouteToAdmin()
    {
        return view('user.location.index');
    }

    /**
     * Get all admins who have handled bookings for this user
     */
    public function getAdminsWithLocation()
    {
        try {
            $user = Auth::guard('web')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized'
                ], 401);
            }
            
            // Geocode user's address if needed
            if ((!$user->latitude || !$user->longitude) && $user->address) {
                try {
                    $userCoords = $this->geocodeService->geocodeAddress($user->address);
                    if ($userCoords) {
                        $user->update([
                            'latitude' => $userCoords['latitude'],
                            'longitude' => $userCoords['longitude']
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to geocode user address: ' . $e->getMessage());
                }
            }
            
            // Get ALL available admins/branches so users can see all options
            // Group by branch_address to avoid duplicate markers for same location
            $allAdmins = Admin::query()
                ->whereNotNull('address')
                ->get();
            
            // Group admins by branch address
            $branchGroups = $allAdmins->groupBy('branch_address');
            
            $branches = collect();
            
            foreach ($branchGroups as $branchAddress => $adminsInBranch) {
                // Use the first admin for this branch location
                $representativeAdmin = $adminsInBranch->first();
                
                // Geocode admin address if needed
                if ((!$representativeAdmin->latitude || !$representativeAdmin->longitude) && $representativeAdmin->address) {
                    try {
                        $coordinates = $this->geocodeService->geocodeAddress($representativeAdmin->address);
                        if ($coordinates) {
                            // Update all admins at this branch with the same coordinates
                            foreach ($adminsInBranch as $admin) {
                                $admin->update([
                                    'latitude' => $coordinates['latitude'],
                                    'longitude' => $coordinates['longitude'],
                                    'location_updated_at' => now()
                                ]);
                            }
                            $representativeAdmin->latitude = $coordinates['latitude'];
                            $representativeAdmin->longitude = $coordinates['longitude'];
                        } else {
                            continue;
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to geocode branch address: ' . $e->getMessage());
                        continue;
                    }
                }
                
                // Get all admin names and phones for this branch
                $adminNames = $adminsInBranch->map(fn($a) => $a->fname . ' ' . $a->lname)->implode(', ');
                $adminPhones = $adminsInBranch->pluck('phone')->unique()->implode(', ');
                
                $branchData = [
                    'id' => $representativeAdmin->id,
                    'admin_ids' => $adminsInBranch->pluck('id')->toArray(), // All admin IDs at this branch
                    'name' => $adminNames, // All admin names
                    'branch_name' => $representativeAdmin->admin_name,
                    'branch_address' => $branchAddress,
                    'phone' => $adminPhones, // All phone numbers
                    'address' => $representativeAdmin->address,
                    'latitude' => (float) $representativeAdmin->latitude,
                    'longitude' => (float) $representativeAdmin->longitude,
                    'admin_count' => $adminsInBranch->count(),
                    'updated_at' => $representativeAdmin->location_updated_at 
                        ? $representativeAdmin->location_updated_at->format('M d, h:i A') 
                        : 'N/A'
                ];

                // Calculate distance and ETA if user has coordinates
                if ($user->latitude && $user->longitude) {
                    $distance = $this->calculateDistance(
                        $user->latitude,
                        $user->longitude,
                        $representativeAdmin->latitude,
                        $representativeAdmin->longitude
                    );
                    
                    $roadFactor = 1.4;
                    $actualDistance = $distance * $roadFactor;
                    $averageSpeed = 30;
                    $travelTimeMinutes = ($actualDistance / $averageSpeed) * 60;
                    
                    $branchData['distance_km'] = round($actualDistance, 2);
                    $branchData['eta_minutes'] = round($travelTimeMinutes, 1);
                    $branchData['eta'] = now('Asia/Manila')->addMinutes($travelTimeMinutes)->format('h:i A');
                }

                $branches->push($branchData);
            }
            
            $admins = $branches->values();

            return response()->json([
                'success' => true,
                'admins' => $admins,
                'user_location' => $user->latitude && $user->longitude ? [
                    'latitude' => (float) $user->latitude,
                    'longitude' => (float) $user->longitude,
                    'name' => $user->fname . ' ' . $user->lname
                ] : null
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getAdminsWithLocation: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load shops',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate distance between two points using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km
        
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return round($earthRadius * $c, 2);
    }
}
