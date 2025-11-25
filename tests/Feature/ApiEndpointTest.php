<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiEndpointTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that /api/users endpoint requires admin authentication
     */
    public function test_api_users_requires_admin_authentication(): void
    {
        // Unauthenticated request should redirect to admin login
        $response = $this->get('/api/users');
        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Test that regular users cannot access /api/users endpoint
     */
    public function test_regular_user_cannot_access_api_users(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $response = $this->get('/api/users');
        
        // Should redirect to user dashboard with error
        $response->assertRedirect(route('user.dashboard'));
        $response->assertSessionHas('error', 'Users cannot access admin pages.');
    }

    /**
     * Test that admin can access /api/users endpoint and receives proper response
     */
    public function test_admin_can_access_api_users(): void
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // Create some test users
        User::factory()->count(3)->create();

        $response = $this->get('/api/users');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'users' => [
                    '*' => [
                        'id',
                        'name',
                        'phone',
                        'address'
                    ]
                ]
            ]
        ]);

        $json = $response->json();
        $this->assertTrue($json['success']);
        $this->assertEquals('Users retrieved successfully', $json['message']);
        $this->assertCount(3, $json['data']['users']);
    }

    /**
     * Test that API endpoint uses controller method instead of inline closure
     */
    public function test_api_endpoint_uses_controller_method(): void
    {
        // Read the routes file
        $routesContent = file_get_contents(base_path('routes/web.php'));
        
        // Verify it uses controller method (updated to AdminRouteController after refactoring)
        $this->assertStringContainsString(
            '[AdminRouteController::class, \'getUsersList\']',
            $routesContent,
            'API endpoint should use AdminRouteController::getUsersList method'
        );
        
        // Verify it doesn't use inline closure
        $this->assertStringNotContainsString(
            'Route::get(\'/api/users\', function()',
            $routesContent,
            'API endpoint should not use inline closure'
        );
    }
}
