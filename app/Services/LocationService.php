<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * LocationService
 * 
 * Provides shared location-related functionality including distance calculations,
 * route calculations, and GeoJSON geometry creation.
 */
class LocationService
{
    /**
     * Calculate distance between two points using Haversine formula
     * 
     * The Haversine formula calculates the great-circle distance between two points
     * on a sphere given their longitudes and latitudes.
     * 
     * @param float $lat1 Latitude of first point in degrees
     * @param float $lon1 Longitude of first point in degrees
     * @param float $lat2 Latitude of second point in degrees
     * @param float $lon2 Longitude of second point in degrees
     * @return float Distance in kilometers
     * @throws \InvalidArgumentException if coordinates are invalid
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        // Validate coordinates
        if (!$this->isValidCoordinate($lat1, $lon1) || !$this->isValidCoordinate($lat2, $lon2)) {
            throw new \InvalidArgumentException('Invalid coordinates provided for distance calculation');
        }
        
        $earthRadius = 6371; // Earth's radius in kilometers
        
        // Convert degrees to radians
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);
        
        // Calculate differences
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        // Haversine formula
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        $distance = $earthRadius * $c;
        
        return round($distance, 2);
    }

    /**
     * Calculate route with distance, time, ETA, and geometry
     * 
     * This method calculates a straight-line route between two points with
     * estimated travel time based on average speed. It applies a road factor
     * to account for the fact that actual roads are typically longer than
     * straight-line distances.
     * 
     * @param float $fromLat Starting latitude in degrees
     * @param float $fromLon Starting longitude in degrees
     * @param float $toLat Destination latitude in degrees
     * @param float $toLon Destination longitude in degrees
     * @param float $averageSpeed Average travel speed in km/h (default: 30)
     * @return array Route information including distance, time, ETA, and geometry
     * @throws \InvalidArgumentException if coordinates are invalid or speed is non-positive
     */
    public function calculateRoute(
        float $fromLat,
        float $fromLon,
        float $toLat,
        float $toLon,
        float $averageSpeed = 30
    ): array {
        // Validate coordinates
        if (!$this->isValidCoordinate($fromLat, $fromLon) || !$this->isValidCoordinate($toLat, $toLon)) {
            throw new \InvalidArgumentException('Invalid coordinates provided for route calculation');
        }
        
        // Validate speed
        if ($averageSpeed <= 0) {
            throw new \InvalidArgumentException('Average speed must be positive');
        }
        // Calculate straight-line distance using Haversine formula
        $earthRadius = 6371000; // Earth's radius in meters
        
        $latFrom = deg2rad($fromLat);
        $lonFrom = deg2rad($fromLon);
        $latTo = deg2rad($toLat);
        $lonTo = deg2rad($toLon);
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        $straightDistance = $earthRadius * $c; // in meters
        
        // Apply road factor (roads are typically 1.3-1.5x longer than straight line)
        // Using 1.4 as a reasonable estimate for city driving
        $roadFactor = 1.4;
        $actualDistance = $straightDistance * $roadFactor;
        
        // Calculate estimated time based on average speed
        // time = distance / speed
        $time = ($actualDistance / 1000) / $averageSpeed * 3600; // in seconds
        
        // Calculate ETA using application's configured timezone
        $currentTime = now();
        $etaTime = $currentTime->copy()->addSeconds($time);
        
        // Create GeoJSON LineString geometry
        $geometry = $this->createLineGeometry($fromLat, $fromLon, $toLat, $toLon);
        
        return [
            'distance' => $actualDistance, // in meters
            'time' => $time, // in seconds
            'geometry' => $geometry,
            'distance_km' => round($actualDistance / 1000, 2),
            'time_minutes' => round($time / 60, 1),
            'eta' => $etaTime->format('h:i A'),
            'current_time' => $currentTime->format('h:i A'),
            'method' => 'estimated'
        ];
    }

    /**
     * Create GeoJSON LineString geometry
     * 
     * Creates a GeoJSON LineString geometry object representing a straight line
     * between two points. This is useful for visualizing routes on maps.
     * 
     * Note: GeoJSON uses [longitude, latitude] order, not [latitude, longitude]
     * 
     * @param float $fromLat Starting latitude in degrees
     * @param float $fromLon Starting longitude in degrees
     * @param float $toLat Destination latitude in degrees
     * @param float $toLon Destination longitude in degrees
     * @return array GeoJSON LineString geometry object
     * @throws \InvalidArgumentException if coordinates are invalid
     */
    public function createLineGeometry(
        float $fromLat,
        float $fromLon,
        float $toLat,
        float $toLon
    ): array {
        // Validate coordinates
        if (!$this->isValidCoordinate($fromLat, $fromLon) || !$this->isValidCoordinate($toLat, $toLon)) {
            throw new \InvalidArgumentException('Invalid coordinates provided for geometry creation');
        }
        
        return [
            'type' => 'LineString',
            'coordinates' => [
                [$fromLon, $fromLat], // GeoJSON uses [lon, lat] order
                [$toLon, $toLat]
            ]
        ];
    }
    
    /**
     * Validate that coordinates are within valid ranges
     * 
     * @param float $lat Latitude in degrees
     * @param float $lon Longitude in degrees
     * @return bool True if coordinates are valid
     */
    private function isValidCoordinate(float $lat, float $lon): bool
    {
        // Latitude must be between -90 and 90
        // Longitude must be between -180 and 180
        return $lat >= -90 && $lat <= 90 && $lon >= -180 && $lon <= 180;
    }
}
