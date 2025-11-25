<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\LocationService;
use App\Services\GeocodeService;

class RouteController extends Controller
{
    use \App\Traits\Responses;

    public function __construct(
        private LocationService $locationService,
        private GeocodeService $geocodeService,
    ) {}

    /**
     * Show route to user page
     */
    public function showRouteToUser()
    {
        return view('admin.routing.route-to-user');
    }

    /**
     * Get route from admin to user with ETA
     */
    public function getRouteToUser($userId)
    {
        $admin = Auth::guard('admin')->user();
        $user = \App\Models\User::find($userId);

        if (!$admin || !$user) {
            return $this->errorResponse('Admin or User not found', [], 404);
        }

        // Geocode addresses if coordinates don't exist (only when actively requesting route)
        if (empty($admin->latitude) || empty($admin->longitude)) {
            if (!$admin->address) {
                Log::warning('Route calculation failed: Admin address not set', ['admin_id' => $admin->id]);
                return $this->errorResponse('Admin address is not set. Please update your profile with a valid address.', [], 400);
            }

            Log::info('Geocoding admin address for route calculation', ['admin_id' => $admin->id, 'address' => $admin->address]);
            $adminCoords = $this->geocodeService->geocodeAddress($admin->address);

            if ($adminCoords && isset($adminCoords['latitude']) && isset($adminCoords['longitude'])) {
                // Validate coordinates before updating
                if (is_numeric($adminCoords['latitude']) && is_numeric($adminCoords['longitude'])) {
                    $admin->update([
                        'latitude' => $adminCoords['latitude'],
                        'longitude' => $adminCoords['longitude'],
                        'location_updated_at' => now()
                    ]);
                    Log::info('Admin location geocoded successfully', ['admin_id' => $admin->id]);
                } else {
                    Log::warning('Geocoding returned invalid coordinates', [
                        'admin_id' => $admin->id,
                        'coordinates' => $adminCoords,
                        'reason' => 'non_numeric'
                    ]);
                    return $this->errorResponse('Unable to determine admin location. The geocoding service returned invalid coordinates.', [], 400);
                }
            } else {
                // Geocoding failed - log and return graceful error
                Log::warning('Geocoding failed for admin address', [
                    'admin_id' => $admin->id,
                    'address' => $admin->address,
                    'reason' => 'geocoding_returned_null'
                ]);
                return $this->errorResponse('Unable to geocode admin address. Please verify the address is correct and try again.', [], 400);
            }
        }

        if (empty($user->latitude) || empty($user->longitude)) {
            if (!$user->address) {
                Log::warning('Route calculation failed: User address not set', ['user_id' => $user->id]);
                return $this->errorResponse('User address is not set. The user needs to update their profile with a valid address.', [], 400);
            }

            Log::info('Geocoding user address for route calculation', ['user_id' => $user->id, 'address' => $user->address]);
            $userCoords = $this->geocodeService->geocodeAddress($user->address);

            if ($userCoords && isset($userCoords['latitude']) && isset($userCoords['longitude'])) {
                // Validate coordinates before updating
                if (is_numeric($userCoords['latitude']) && is_numeric($userCoords['longitude'])) {
                    $user->update([
                        'latitude' => $userCoords['latitude'],
                        'longitude' => $userCoords['longitude']
                    ]);
                    Log::info('User location geocoded successfully', ['user_id' => $user->id]);
                } else {
                    Log::warning('Geocoding returned invalid coordinates', [
                        'user_id' => $user->id,
                        'coordinates' => $userCoords,
                        'reason' => 'non_numeric'
                    ]);
                    return $this->errorResponse('Unable to determine user location. The geocoding service returned invalid coordinates.', [], 400);
                }
            } else {
                // Geocoding failed - log and return graceful error
                Log::warning('Geocoding failed for user address', [
                    'user_id' => $user->id,
                    'address' => $user->address,
                    'reason' => 'geocoding_returned_null'
                ]);
                return $this->errorResponse('Unable to geocode user address. Please verify the address is correct and try again.', [], 400);
            }
        }

        // Verify both coordinates exist and are valid before attempting route calculation
        if (
            empty($admin->latitude) || empty($admin->longitude) || empty($user->latitude) || empty($user->longitude) ||
            !is_numeric($admin->latitude) || !is_numeric($admin->longitude) ||
            !is_numeric($user->latitude) || !is_numeric($user->longitude)
        ) {
            Log::error('Route calculation failed: Invalid coordinates after geocoding', [
                'admin_id' => $admin->id,
                'user_id' => $user->id,
                'admin_coords' => ['lat' => $admin->latitude, 'lon' => $admin->longitude],
                'user_coords' => ['lat' => $user->latitude, 'lon' => $user->longitude]
            ]);
            return $this->errorResponse('Cannot calculate route: Location coordinates are missing or invalid after geocoding attempt.', [], 400);
        }

        // Get route from Geoapify
        try {
            $routeData = $this->getRouteFromGeoapify(
                (float) $admin->latitude,
                (float) $admin->longitude,
                (float) $user->latitude,
                (float) $user->longitude
            );

            if (!$routeData) {
                Log::error('Route calculation returned no data', [
                    'admin_id' => $admin->id,
                    'user_id' => $user->id
                ]);
                return $this->errorResponse('Could not calculate route. Both routing API and fallback calculation failed.', [], 400);
            }

            return $this->successResponse('Route calculated successfully', [
                'admin' => [
                    'name' => $admin->fname . ' ' . $admin->lname,
                    'latitude' => (float) $admin->latitude,
                    'longitude' => (float) $admin->longitude,
                    'address' => $admin->address
                ],
                'user' => [
                    'name' => $user->fname . ' ' . $user->lname,
                    'latitude' => (float) $user->latitude,
                    'longitude' => (float) $user->longitude,
                    'address' => $user->address
                ],
                'route' => $routeData
            ]);
        } catch (\InvalidArgumentException $e) {
            Log::error('Route calculation failed: Invalid coordinates', [
                'admin_id' => $admin->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'reason' => 'invalid_argument'
            ]);
            return $this->errorResponse('Cannot calculate route: The coordinates provided are invalid. ' . $e->getMessage(), [], 400);
        } catch (\Exception $e) {
            Log::error('Route calculation failed: Unexpected error', [
                'admin_id' => $admin->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'reason' => 'exception'
            ]);
            return $this->errorResponse('An unexpected error occurred while calculating the route. Please try again.', [], 500);
        }
    }

    /**
     * Get route using simple distance calculation (fallback)
     */
    private function getRouteFromGeoapify($fromLat, $fromLon, $toLat, $toLon)
    {
        try {
            $apiKey = config('services.geoapify.api_key');

            // Try Geoapify routing first
            $response = Http::withOptions([
                'verify' => false,
            ])->timeout(10)->get('https://api.geoapify.com/v1/routing', [
                'waypoints' => "{$fromLat},{$fromLon}|{$toLat},{$toLon}",
                'mode' => 'drive',
                'apiKey' => $apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data['features'][0])) {
                    $feature = $data['features'][0];
                    $properties = $feature['properties'];

                    // Calculate ETA using application's configured timezone
                    $currentTime = now();
                    $etaTime = $currentTime->copy()->addSeconds($properties['time']);

                    return [
                        'distance' => $properties['distance'], // in meters
                        'time' => $properties['time'], // in seconds
                        'geometry' => $feature['geometry'],
                        'distance_km' => round($properties['distance'] / 1000, 2),
                        'time_minutes' => round($properties['time'] / 60, 1),
                        'eta' => $etaTime->format('h:i A'),
                        'current_time' => $currentTime->format('h:i A'),
                        'method' => 'routing'
                    ];
                }
            }

            // Fallback to LocationService for straight-line distance calculation
            Log::warning('Routing API failed, using distance calculation fallback', [
                'reason' => 'no_features_in_response'
            ]);
            return $this->locationService->calculateRoute($fromLat, $fromLon, $toLat, $toLon);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Routing API connection failed, using fallback', [
                'error' => $e->getMessage(),
                'reason' => 'network_error'
            ]);
            return $this->locationService->calculateRoute($fromLat, $fromLon, $toLat, $toLon);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('Routing API request failed, using fallback', [
                'error' => $e->getMessage(),
                'status' => $e->response ? $e->response->status() : 'unknown',
                'reason' => 'api_error'
            ]);
            return $this->locationService->calculateRoute($fromLat, $fromLon, $toLat, $toLon);
        } catch (\Exception $e) {
            Log::error('Routing exception, using fallback', [
                'error' => $e->getMessage(),
                'reason' => 'exception'
            ]);
            return $this->locationService->calculateRoute($fromLat, $fromLon, $toLat, $toLon);
        }
    }

    /**
     * Get list of users for API
     */
    public function getUsersList(Request $request)
    {
        try {
            $users = \App\Models\User::select('id', 'username', 'fname', 'lname', 'phone', 'address')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->fname . ' ' . $user->lname,
                        'phone' => $user->phone,
                        'address' => $user->address
                    ];
                });

            return $this->successResponse('Users retrieved successfully', [
                'users' => $users
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve users: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve users', [], 500);
        }
    }
}
