<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MapConfigurationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that Geoapify API key is accessible via config helper
     */
    public function test_geoapify_api_key_is_accessible_via_config(): void
    {
        $apiKey = config('services.geoapify.api_key');
        
        $this->assertNotNull($apiKey, 'Geoapify API key should be configured');
        $this->assertIsString($apiKey, 'Geoapify API key should be a string');
        $this->assertNotEmpty($apiKey, 'Geoapify API key should not be empty');
    }

    /**
     * Test that admin route-to-user page loads correctly
     */
    public function test_admin_route_to_user_page_loads(): void
    {
        $admin = Admin::factory()->create();
        
        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.route-to-user'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.routing.route-to-user');
        
        // Verify the page contains the API key in data attribute
        $apiKey = config('services.geoapify.api_key');
        $response->assertSee("data-geoapify-key=\"{$apiKey}\"", false);
    }

    /**
     * Test that user track-admin page loads correctly
     */
    public function test_user_track_admin_page_loads(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user, 'web')
            ->get(route('user.track-admin'));
        
        $response->assertStatus(200);
        $response->assertViewIs('user.tracking.track-admin');
        
        // Verify the page contains the API key in data attribute
        $apiKey = config('services.geoapify.api_key');
        $response->assertSee("data-geoapify-key=\"{$apiKey}\"", false);
    }

    /**
     * Test that both pages use consistent API key configuration
     */
    public function test_both_pages_use_consistent_api_key_configuration(): void
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        
        // Test admin page
        $adminResponse = $this->actingAs($admin, 'admin')
            ->get(route('admin.route-to-user'));
        
        // Logout admin before testing user page
        auth()->guard('admin')->logout();
        
        // Test user page
        $userResponse = $this->actingAs($user, 'web')
            ->get(route('user.track-admin'));
        
        // Both should contain the same API key value in data attribute
        $apiKey = config('services.geoapify.api_key');
        $adminResponse->assertSee("data-geoapify-key=\"{$apiKey}\"", false);
        $userResponse->assertSee("data-geoapify-key=\"{$apiKey}\"", false);
    }
}
