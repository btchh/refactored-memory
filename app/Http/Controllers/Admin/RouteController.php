<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\GeocodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RouteController extends Controller
{
    public function __construct(
        private GeocodeService $geocodeService
    ) {}

    /**
     * Show route to user page (delivery tracking)
     */
    public function showRouteToUser()
    {
        return view('admin.route-to-user');
    }

    /**
     * Get all users who have bookings with this admin's branch
     */
    public function getUsersWithLocation()
    {
        try {
            $admin = Auth::guard('admin')->user();
            
            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized'
                ], 401);
            }
            
            // Geocode admin's address if needed
            if ((!$admin->latitude || !$admin->longitude) && $admin->address) {
                try {
                    $adminCoords = $this->geocodeService->geocodeAddress($admin->address);
                    if ($adminCoords) {
                        $admin->update([
                            'latitude' => $adminCoords['latitude'],
                            'longitude' => $adminCoords['longitude'],
                            'location_updated_at' => now()
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to geocode admin address: ' . $e->getMessage());
                }
            }
            
            // Get distinct users who have bookings with this admin's branch
            $userIds = \App\Models\Transaction::where('admin_id', $admin->id)
                ->distinct()
                ->pluck('user_id');
            
            if ($userIds->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'users' => [],
                    'admin_location' => $admin->latitude && $admin->longitude ? [
                        'latitude' => (float) $admin->latitude,
                        'longitude' => (float) $admin->longitude
                    ] : null,
                    'message' => 'No customers have booked with your branch yet'
                ]);
            }
            
            // Get users with addresses
            $users = \App\Models\User::whereIn('id', $userIds)
                ->whereNotNull('address')
                ->get()
                ->map(function ($user) use ($admin) {
                    // Geocode user address if needed
                    if ((!$user->latitude || !$user->longitude) && $user->address) {
                        try {
                            $coordinates = $this->geocodeService->geocodeAddress($user->address);
                            if ($coordinates) {
                                $user->update([
                                    'latitude' => $coordinates['latitude'],
                                    'longitude' => $coordinates['longitude']
                                ]);
                                $user->latitude = $coordinates['latitude'];
                                $user->longitude = $coordinates['longitude'];
                            } else {
                                return null;
                            }
                        } catch (\Exception $e) {
                            \Log::error('Failed to geocode user address: ' . $e->getMessage());
                            return null;
                        }
                    }

                    $userData = [
                        'id' => $user->id,
                        'name' => $user->fname . ' ' . $user->lname,
                        'phone' => $user->phone,
                        'address' => $user->address,
                        'latitude' => (float) $user->latitude,
                        'longitude' => (float) $user->longitude
                    ];

                    // Calculate distance and ETA if admin has coordinates
                    if ($admin->latitude && $admin->longitude) {
                        $distance = $this->calculateDistance(
                            $admin->latitude,
                            $admin->longitude,
                            $user->latitude,
                            $user->longitude
                        );
                        
                        $roadFactor = 1.4;
                        $actualDistance = $distance * $roadFactor;
                        $averageSpeed = 30;
                        $travelTimeMinutes = ($actualDistance / $averageSpeed) * 60;
                        
                        $userData['distance_km'] = round($actualDistance, 2);
                        $userData['eta_minutes'] = round($travelTimeMinutes, 1);
                        $userData['eta'] = now('Asia/Manila')->addMinutes($travelTimeMinutes)->format('h:i A');
                    }

                    return $userData;
                })
                ->filter()
                ->values();

            return response()->json([
                'success' => true,
                'users' => $users,
                'admin_location' => $admin->latitude && $admin->longitude ? [
                    'latitude' => (float) $admin->latitude,
                    'longitude' => (float) $admin->longitude
                ] : null
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getUsersWithLocation: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load users',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get route from admin to specific user
     */
    public function getRouteToUser($userId)
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Geocode admin's address if needed
        if ((!$admin->latitude || !$admin->longitude) && $admin->address) {
            $adminCoords = $this->geocodeService->geocodeAddress($admin->address);
            if ($adminCoords) {
                $admin->update([
                    'latitude' => $adminCoords['latitude'],
                    'longitude' => $adminCoords['longitude']
                ]);
            }
        }

        $user = \App\Models\User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Geocode user's address if needed
        if ((!$user->latitude || !$user->longitude) && $user->address) {
            $userCoords = $this->geocodeService->geocodeAddress($user->address);
            if ($userCoords) {
                $user->update([
                    'latitude' => $userCoords['latitude'],
                    'longitude' => $userCoords['longitude']
                ]);
            }
        }

        if (!$admin->latitude || !$admin->longitude || !$user->latitude || !$user->longitude) {
            return response()->json(['error' => 'Unable to geocode addresses'], 400);
        }

        $distance = $this->calculateDistance(
            $admin->latitude,
            $admin->longitude,
            $user->latitude,
            $user->longitude
        );

        // Apply road factor
        $roadFactor = 1.4;
        $actualDistance = $distance * $roadFactor;

        // Calculate ETA (30 km/h average)
        $averageSpeed = 30;
        $travelTimeMinutes = ($actualDistance / $averageSpeed) * 60;

        return response()->json([
            'success' => true,
            'admin' => [
                'name' => $admin->fname . ' ' . $admin->lname,
                'latitude' => (float) $admin->latitude,
                'longitude' => (float) $admin->longitude,
                'address' => $admin->address
            ],
            'user' => [
                'id' => $user->id,
                'name' => $user->fname . ' ' . $user->lname,
                'latitude' => (float) $user->latitude,
                'longitude' => (float) $user->longitude,
                'address' => $user->address,
                'phone' => $user->phone
            ],
            'distance_km' => round($actualDistance, 2),
            'eta_minutes' => round($travelTimeMinutes, 1),
            'eta' => now('Asia/Manila')->addMinutes($travelTimeMinutes)->format('h:i A')
        ]);
    }

    /**
     * Show route to customer for a booking
     */
    public function showRoute($bookingId)
    {
        $booking = Transaction::with('user')->findOrFail($bookingId);
        
        return view('admin.route', compact('booking'));
    }

    /**
     * Get route data from admin to customer
     */
    public function getRouteData($bookingId)
    {
        $admin = Auth::guard('admin')->user();
        $booking = Transaction::with('user')->findOrFail($bookingId);

        // Geocode admin's address if needed
        if ((!$admin->latitude || !$admin->longitude) && $admin->address) {
            $adminCoords = $this->geocodeService->geocodeAddress($admin->address);
            if ($adminCoords) {
                $admin->update([
                    'latitude' => $adminCoords['latitude'],
                    'longitude' => $adminCoords['longitude']
                ]);
            }
        }

        // Use booking's pickup address coordinates
        $customerLat = $booking->latitude;
        $customerLng = $booking->longitude;

        // If booking doesn't have coordinates, try to geocode
        if (!$customerLat || !$customerLng) {
            $customerCoords = $this->geocodeService->geocodeAddress($booking->pickup_address);
            if ($customerCoords) {
                $booking->update([
                    'latitude' => $customerCoords['latitude'],
                    'longitude' => $customerCoords['longitude']
                ]);
                $customerLat = $customerCoords['latitude'];
                $customerLng = $customerCoords['longitude'];
            }
        }

        if (!$admin->latitude || !$admin->longitude || !$customerLat || !$customerLng) {
            return response()->json(['error' => 'Unable to geocode addresses'], 400);
        }

        $distance = $this->calculateDistance(
            $admin->latitude,
            $admin->longitude,
            $customerLat,
            $customerLng
        );

        // Apply road factor
        $roadFactor = 1.4;
        $actualDistance = $distance * $roadFactor;

        // Calculate ETA (30 km/h average)
        $averageSpeed = 30;
        $travelTimeMinutes = ($actualDistance / $averageSpeed) * 60;

        return response()->json([
            'success' => true,
            'admin' => [
                'name' => $admin->fname . ' ' . $admin->lname,
                'latitude' => (float) $admin->latitude,
                'longitude' => (float) $admin->longitude,
                'address' => $admin->address
            ],
            'customer' => [
                'name' => $booking->user->fname . ' ' . $booking->user->lname,
                'latitude' => (float) $customerLat,
                'longitude' => (float) $customerLng,
                'address' => $booking->pickup_address,
                'phone' => $booking->user->phone
            ],
            'booking' => [
                'id' => $booking->id,
                'date' => $booking->formatted_date,
                'time' => $booking->formatted_time,
                'service_type' => $booking->item_type,
                'status' => $booking->status
            ],
            'distance_km' => round($actualDistance, 2),
            'eta_minutes' => round($travelTimeMinutes, 1),
            'eta' => now('Asia/Manila')->addMinutes($travelTimeMinutes)->format('h:i A')
        ]);
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
