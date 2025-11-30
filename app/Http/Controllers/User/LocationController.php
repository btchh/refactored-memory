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
            
            // Get distinct admins who have handled bookings for this user
            $adminIds = Transaction::where('user_id', $user->id)
                ->distinct()
                ->pluck('admin_id');
            
            if ($adminIds->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'admins' => [],
                    'user_location' => $user->latitude && $user->longitude ? [
                        'latitude' => (float) $user->latitude,
                        'longitude' => (float) $user->longitude,
                        'name' => $user->fname . ' ' . $user->lname
                    ] : null,
                    'message' => 'No shops have handled your bookings yet'
                ]);
            }

            // Get admins with addresses
            $admins = Admin::whereIn('id', $adminIds)
                ->whereNotNull('address')
                ->get()
                ->map(function ($admin) use ($user) {
                    // Geocode admin address if needed
                    if ((!$admin->latitude || !$admin->longitude) && $admin->address) {
                        try {
                            $coordinates = $this->geocodeService->geocodeAddress($admin->address);
                            if ($coordinates) {
                                $admin->update([
                                    'latitude' => $coordinates['latitude'],
                                    'longitude' => $coordinates['longitude'],
                                    'location_updated_at' => now()
                                ]);
                                $admin->latitude = $coordinates['latitude'];
                                $admin->longitude = $coordinates['longitude'];
                            } else {
                                return null;
                            }
                        } catch (\Exception $e) {
                            \Log::error('Failed to geocode admin address: ' . $e->getMessage());
                            return null;
                        }
                    }

                    $adminData = [
                        'id' => $admin->id,
                        'name' => $admin->fname . ' ' . $admin->lname,
                        'branch_name' => $admin->admin_name,
                        'phone' => $admin->phone,
                        'address' => $admin->address,
                        'latitude' => (float) $admin->latitude,
                        'longitude' => (float) $admin->longitude,
                        'updated_at' => $admin->location_updated_at 
                            ? $admin->location_updated_at->format('M d, h:i A') 
                            : 'N/A'
                    ];

                    // Calculate distance and ETA if user has coordinates
                    if ($user->latitude && $user->longitude) {
                        $distance = $this->calculateDistance(
                            $user->latitude,
                            $user->longitude,
                            $admin->latitude,
                            $admin->longitude
                        );
                        
                        $roadFactor = 1.4;
                        $actualDistance = $distance * $roadFactor;
                        $averageSpeed = 30;
                        $travelTimeMinutes = ($actualDistance / $averageSpeed) * 60;
                        
                        $adminData['distance_km'] = round($actualDistance, 2);
                        $adminData['eta_minutes'] = round($travelTimeMinutes, 1);
                        $adminData['eta'] = now('Asia/Manila')->addMinutes($travelTimeMinutes)->format('h:i A');
                    }

                    return $adminData;
                })
                ->filter()
                ->values();

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
