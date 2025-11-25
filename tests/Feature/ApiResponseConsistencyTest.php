<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test API response structure consistency across all endpoints
 * Validates Requirements 5.1, 5.2, 5.3, 8.1
 */
class ApiResponseConsistencyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that successful API responses have consistent structure
     * Validates: Requirements 5.1
     */
    public function test_successful_api_responses_have_consistent_structure(): void
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // Test /api/users endpoint
        User::factory()->count(2)->create();
        $response = $this->get('/api/users');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);

        $json = $response->json();
        $this->assertTrue($json['success']);
        $this->assertIsString($json['message']);
        $this->assertIsArray($json['data']);
    }

    /**
     * Test that error API responses have consistent structure
     * Validates: Requirements 5.2, 8.1
     */
    public function test_error_api_responses_have_consistent_structure(): void
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // Test route calculation with invalid user ID
        $response = $this->get('/admin/get-route/99999');

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'success',
            'message',
            'errors'
        ]);

        $json = $response->json();
        $this->assertFalse($json['success']);
        $this->assertIsString($json['message']);
        $this->assertIsArray($json['errors']);
    }

    /**
     * Test that validation errors have consistent structure
     * Validates: Requirements 5.2, 5.3
     */
    public function test_validation_error_responses_have_consistent_structure(): void
    {
        // Test OTP verification with invalid data
        $response = $this->postJson('/user/verify-otp', [
            'phone' => '',
            'otp' => '123' // Invalid: should be 6 digits
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message',
            'errors'
        ]);

        $json = $response->json();
        $this->assertFalse($json['success']);
        $this->assertIsString($json['message']);
        $this->assertIsArray($json['errors']);
    }

    /**
     * Test that admin location endpoint returns consistent structure
     * Validates: Requirements 5.1, 5.3
     */
    public function test_admin_location_endpoint_has_consistent_structure(): void
    {
        $user = User::factory()->create([
            'latitude' => 14.5995,
            'longitude' => 120.9842
        ]);
        $this->actingAs($user, 'web');

        Admin::factory()->create([
            'latitude' => 14.6091,
            'longitude' => 121.0223,
            'address' => 'Test Address'
        ]);

        $response = $this->get('/user/admin-location');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'admins',
                'user_location'
            ]
        ]);

        $json = $response->json();
        $this->assertTrue($json['success']);
        $this->assertIsString($json['message']);
        $this->assertIsArray($json['data']);
    }

    /**
     * Test that OTP endpoints return consistent structure
     * Validates: Requirements 5.1, 5.2, 5.3
     */
    public function test_otp_endpoints_have_consistent_structure(): void
    {
        // Test successful OTP send (validation passes)
        $response = $this->postJson('/user/send-registration-otp', [
            'phone' => '09123456789',
            'email' => 'test@example.com'
        ]);

        // Should return consistent structure regardless of success/failure
        $response->assertJsonStructure([
            'success',
            'message'
        ]);

        $json = $response->json();
        $this->assertIsBool($json['success']);
        $this->assertIsString($json['message']);
    }

    /**
     * Test that all API endpoints use Responses trait methods
     * Validates: Requirements 5.3, 8.1
     */
    public function test_controllers_use_responses_trait(): void
    {
        $adminAuthController = new \App\Http\Controllers\Admin\AuthController(
            app(\App\Services\Auth\AdminAuthService::class),
            app(\App\Services\Auth\OtpService::class),
            app(\App\Services\Admin\AdminProfileService::class)
        );

        $userAuthController = new \App\Http\Controllers\User\AuthController(
            app(\App\Services\Auth\UserAuthService::class),
            app(\App\Services\Auth\OtpService::class),
            app(\App\Services\User\UserManagementService::class),
            app(\App\Services\User\UserProfileService::class)
        );

        $adminRouteController = new \App\Http\Controllers\Admin\RouteController(
            app(\App\Services\LocationService::class),
            app(\App\Services\GeocodeService::class)
        );

        $userTrackingController = new \App\Http\Controllers\User\TrackingController(
            app(\App\Services\GeocodeService::class),
            app(\App\Services\LocationService::class)
        );

        // Verify all controllers use the Responses trait
        $this->assertContains(
            \App\Traits\Responses::class,
            class_uses($adminAuthController)
        );

        $this->assertContains(
            \App\Traits\Responses::class,
            class_uses($userAuthController)
        );

        $this->assertContains(
            \App\Traits\Responses::class,
            class_uses($adminRouteController)
        );

        $this->assertContains(
            \App\Traits\Responses::class,
            class_uses($userTrackingController)
        );
    }
}
