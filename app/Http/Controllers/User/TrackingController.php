<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\GeocodeService;
use App\Services\LocationService;

class TrackingController extends Controller
{
    use \App\Traits\Responses;

    public function __construct(
        private GeocodeService $geocodeService,
        private LocationService $locationService,
    ) {}

    /**
     * Show admin location tracking page
     */
    public function showTrackAdmin()
    {
        return view('user.tracking.track-admin');
    }

    /**
     * Get admin location data with ETA
     *
     * Performance notes:
     * - Uses select() to fetch only required columns
     * - Geocoding only occurs when force_geocode=true and coordinates are missing
     * - Individual admin updates are intentional (geocoding is expensive and should be cached)
     * - Query logging is enabled in development (see AppServiceProvider)
     */
    public function getAdminLocation(Request $request)
    {
        $user = Auth::guard('web')->user();

        if (!$user) {
            return $this->errorResponse('User not authenticated', [], 401);
        }

        $forceGeocode = $request->boolean('force_geocode', false);

        // Only geocode user's address if forced or coordinates are missing
        if ((!$user->latitude || !$user->longitude) && $user->address && $forceGeocode) {
            Log::info('Geocoding user address', ['user_id' => $user->id, 'address' => $user->address]);
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
                    Log::warning('Geocoding returned invalid coordinates for user', [
                        'user_id' => $user->id,
                        'coordinates' => $userCoords,
                        'reason' => 'non_numeric'
                    ]);
                }
            } else {
                // Geocoding failed - log but continue without user coordinates
                Log::warning('Geocoding failed for user address', [
                    'user_id' => $user->id,
                    'address' => $user->address,
                    'reason' => 'geocoding_returned_null'
                ]);
            }
        }

        // Get all admins with addresses and existing coordinates
        // Performance optimization: Using select() to only fetch needed columns
        // This reduces memory usage and network transfer when dealing with many admins
        // Note: No eager loading needed as Admin model has no relationships accessed here
        $admins = \App\Models\Admin::select('id', 'fname', 'lname', 'phone', 'address', 'latitude', 'longitude', 'location_updated_at')
            ->whereNotNull('address')
            ->get()
            ->map(function ($admin) use ($user, $forceGeocode) {
                // Skip admins without address
                if (empty($admin->address)) {
                    return null;
                }

                // Only geocode if forced and coordinates don't exist
                if ((empty($admin->latitude) || empty($admin->longitude)) && $forceGeocode) {
                    Log::info('Geocoding admin address', ['admin_id' => $admin->id, 'address' => $admin->address]);
                    $coordinates = $this->geocodeService->geocodeAddress($admin->address);

                    if ($coordinates) {
                        // Validate coordinates before updating
                        if (!isset($coordinates['latitude']) || !isset($coordinates['longitude'])) {
                            Log::warning('Geocoding returned incomplete coordinates for admin', [
                                'admin_id' => $admin->id,
                                'coordinates' => $coordinates,
                                'reason' => 'missing_lat_or_lon'
                            ]);
                            return null;
                        }

                        if (!is_numeric($coordinates['latitude']) || !is_numeric($coordinates['longitude'])) {
                            Log::warning('Geocoding returned non-numeric coordinates for admin', [
                                'admin_id' => $admin->id,
                                'coordinates' => $coordinates,
                                'reason' => 'non_numeric'
                            ]);
                            return null;
                        }

                        // Update admin with geocoded coordinates
                        $admin->update([
                            'latitude' => $coordinates['latitude'],
                            'longitude' => $coordinates['longitude'],
                            'location_updated_at' => now()
                        ]);

                        $admin->latitude = $coordinates['latitude'];
                        $admin->longitude = $coordinates['longitude'];
                        Log::info('Admin location geocoded successfully', ['admin_id' => $admin->id]);
                    } else {
                        // Geocoding failed - log and skip this admin
                        Log::warning('Geocoding failed for admin address', [
                            'admin_id' => $admin->id,
                            'address' => $admin->address,
                            'reason' => 'geocoding_returned_null'
                        ]);
                        return null; // Skip admins with invalid addresses
                    }
                }

                // Skip admins without valid coordinates
                if (
                    empty($admin->latitude) || empty($admin->longitude) ||
                    !is_numeric($admin->latitude) || !is_numeric($admin->longitude)
                ) {
                    return null;
                }

                $adminData = [
                    'id' => $admin->id,
                    'name' => $admin->fname . ' ' . $admin->lname,
                    'phone' => $admin->phone,
                    'address' => $admin->address,
                    'latitude' => (float) $admin->latitude,
                    'longitude' => (float) $admin->longitude,
                    'updated_at' => $admin->location_updated_at ? $admin->location_updated_at->diffForHumans() : 'Never'
                ];

                // Calculate distance and ETA only if user has valid coordinates
                if (
                    $user && $user->latitude && $user->longitude &&
                    is_numeric($user->latitude) && is_numeric($user->longitude)
                ) {
                    try {
                        $route = $this->getRouteFromGeoapify(
                            (float) $user->latitude,
                            (float) $user->longitude,
                            (float) $admin->latitude,
                            (float) $admin->longitude
                        );

                        if ($route) {
                            $adminData['distance_km'] = $route['distance_km'];
                            $adminData['eta_minutes'] = $route['time_minutes'];
                            $adminData['eta'] = $route['eta'];
                            $adminData['current_time'] = $route['current_time'];
                            $adminData['geometry'] = $route['geometry'] ?? null;
                            $adminData['method'] = $route['method'] ?? 'routing';
                        }
                    } catch (\InvalidArgumentException $e) {
                        // Log invalid coordinate error but continue without route data
                        Log::warning('Route calculation failed for admin: Invalid coordinates', [
                            'user_id' => $user->id,
                            'admin_id' => $admin->id,
                            'error' => $e->getMessage(),
                            'reason' => 'invalid_argument'
                        ]);
                    } catch (\Exception $e) {
                        // Log unexpected error but continue without route data
                        Log::error('Route calculation failed for admin: Unexpected error', [
                            'user_id' => $user->id,
                            'admin_id' => $admin->id,
                            'error' => $e->getMessage(),
                            'reason' => 'exception'
                        ]);
                    }
                }

                return $adminData;
            })
            ->filter(); // Remove null entries

        return $this->successResponse('Admin locations retrieved successfully', [
            'admins' => $admins->values(),
            'user_location' => $user && $user->latitude && $user->longitude &&
                is_numeric($user->latitude) && is_numeric($user->longitude) ? [
                    'latitude' => (float) $user->latitude,
                    'longitude' => (float) $user->longitude
                ] : null
        ]);
    }

    /**
     * Get route using Geoapify routing API with fallback to distance calculation
     */
    private function getRouteFromGeoapify($fromLat, $fromLon, $toLat, $toLon)
    {
        try {
            $apiKey = config('services.geoapify.api_key');

            // Try Geoapify routing first
            $response = \Illuminate\Support\Facades\Http::withOptions([
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
}
