<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\LocationService;

class LocationServiceTest extends TestCase
{
    private LocationService $locationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->locationService = new LocationService();
    }

    /**
     * Test distance calculation with known coordinates
     */
    public function test_calculate_distance_with_known_coordinates(): void
    {
        // Distance between Manila (14.5995° N, 120.9842° E) and Quezon City (14.6760° N, 121.0437° E)
        // Expected distance is approximately 10-11 km
        $distance = $this->locationService->calculateDistance(
            14.5995,
            120.9842,
            14.6760,
            121.0437
        );

        $this->assertIsFloat($distance);
        $this->assertGreaterThan(9, $distance);
        $this->assertLessThan(12, $distance);
    }

    /**
     * Test distance calculation is symmetric
     */
    public function test_calculate_distance_is_symmetric(): void
    {
        $lat1 = 14.5995;
        $lon1 = 120.9842;
        $lat2 = 14.6760;
        $lon2 = 121.0437;

        $distance1 = $this->locationService->calculateDistance($lat1, $lon1, $lat2, $lon2);
        $distance2 = $this->locationService->calculateDistance($lat2, $lon2, $lat1, $lon1);

        $this->assertEquals($distance1, $distance2);
    }

    /**
     * Test distance calculation for same location
     */
    public function test_calculate_distance_same_location(): void
    {
        $distance = $this->locationService->calculateDistance(
            14.5995,
            120.9842,
            14.5995,
            120.9842
        );

        $this->assertEquals(0, $distance);
    }

    /**
     * Test route calculation returns required fields
     */
    public function test_calculate_route_returns_required_fields(): void
    {
        $route = $this->locationService->calculateRoute(
            14.5995,
            120.9842,
            14.6760,
            121.0437
        );

        $this->assertIsArray($route);
        $this->assertArrayHasKey('distance', $route);
        $this->assertArrayHasKey('time', $route);
        $this->assertArrayHasKey('geometry', $route);
        $this->assertArrayHasKey('distance_km', $route);
        $this->assertArrayHasKey('time_minutes', $route);
        $this->assertArrayHasKey('eta', $route);
        $this->assertArrayHasKey('current_time', $route);
        $this->assertArrayHasKey('method', $route);
    }

    /**
     * Test route calculation with custom speed
     */
    public function test_calculate_route_with_custom_speed(): void
    {
        $route1 = $this->locationService->calculateRoute(
            14.5995,
            120.9842,
            14.6760,
            121.0437,
            30 // 30 km/h
        );

        $route2 = $this->locationService->calculateRoute(
            14.5995,
            120.9842,
            14.6760,
            121.0437,
            60 // 60 km/h
        );

        // Higher speed should result in less time
        $this->assertGreaterThan($route2['time'], $route1['time']);
    }

    /**
     * Test GeoJSON geometry creation
     */
    public function test_create_line_geometry(): void
    {
        $geometry = $this->locationService->createLineGeometry(
            14.5995,
            120.9842,
            14.6760,
            121.0437
        );

        $this->assertIsArray($geometry);
        $this->assertEquals('LineString', $geometry['type']);
        $this->assertArrayHasKey('coordinates', $geometry);
        $this->assertCount(2, $geometry['coordinates']);
        
        // Verify GeoJSON uses [lon, lat] order
        $this->assertEquals([120.9842, 14.5995], $geometry['coordinates'][0]);
        $this->assertEquals([121.0437, 14.6760], $geometry['coordinates'][1]);
    }

    /**
     * Test route calculation applies road factor
     */
    public function test_route_calculation_applies_road_factor(): void
    {
        $route = $this->locationService->calculateRoute(
            14.5995,
            120.9842,
            14.6760,
            121.0437
        );

        // Calculate straight-line distance
        $straightDistance = $this->locationService->calculateDistance(
            14.5995,
            120.9842,
            14.6760,
            121.0437
        );

        // Route distance should be greater than straight-line distance
        // (because of road factor of 1.4)
        $this->assertGreaterThan($straightDistance, $route['distance_km']);
    }

    /**
     * Test distance calculation rejects invalid latitude
     */
    public function test_calculate_distance_rejects_invalid_latitude(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid coordinates');

        $this->locationService->calculateDistance(
            91, // Invalid latitude (> 90)
            120.9842,
            14.6760,
            121.0437
        );
    }

    /**
     * Test distance calculation rejects invalid longitude
     */
    public function test_calculate_distance_rejects_invalid_longitude(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid coordinates');

        $this->locationService->calculateDistance(
            14.5995,
            181, // Invalid longitude (> 180)
            14.6760,
            121.0437
        );
    }

    /**
     * Test route calculation rejects invalid coordinates
     */
    public function test_calculate_route_rejects_invalid_coordinates(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid coordinates');

        $this->locationService->calculateRoute(
            -91, // Invalid latitude
            120.9842,
            14.6760,
            121.0437
        );
    }

    /**
     * Test route calculation rejects non-positive speed
     */
    public function test_calculate_route_rejects_non_positive_speed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Average speed must be positive');

        $this->locationService->calculateRoute(
            14.5995,
            120.9842,
            14.6760,
            121.0437,
            0 // Invalid speed
        );
    }

    /**
     * Test geometry creation rejects invalid coordinates
     */
    public function test_create_line_geometry_rejects_invalid_coordinates(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid coordinates');

        $this->locationService->createLineGeometry(
            14.5995,
            120.9842,
            200, // Invalid latitude
            121.0437
        );
    }

    /**
     * Test distance calculation accepts boundary values
     */
    public function test_calculate_distance_accepts_boundary_values(): void
    {
        // Test with maximum valid coordinates
        $distance = $this->locationService->calculateDistance(
            90,   // Max latitude
            180,  // Max longitude
            -90,  // Min latitude
            -180  // Min longitude
        );

        $this->assertIsFloat($distance);
        $this->assertGreaterThan(0, $distance);
    }
}
