<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MiddlewareRedirectTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that all dashboard route references are properly prefixed
     * This verifies the fix for undefined route reference in middleware
     */
    public function test_dashboard_routes_exist(): void
    {
        // Verify that admin.dashboard route exists
        $this->assertTrue(
            \Illuminate\Support\Facades\Route::has('admin.dashboard'),
            'admin.dashboard route should exist'
        );

        // Verify that user.dashboard route exists
        $this->assertTrue(
            \Illuminate\Support\Facades\Route::has('user.dashboard'),
            'user.dashboard route should exist'
        );

        // Verify that standalone 'dashboard' route does NOT exist
        // This was the bug - middleware was referencing route('dashboard') which doesn't exist
        $this->assertFalse(
            \Illuminate\Support\Facades\Route::has('dashboard'),
            'Standalone dashboard route should not exist - all dashboard routes must be prefixed'
        );
    }

    /**
     * Test that middleware files reference valid routes
     * This ensures the fix in isAdmin.php uses route('user.dashboard') instead of route('dashboard')
     */
    public function test_middleware_files_reference_valid_routes(): void
    {
        // Read the isAdmin middleware file
        $isAdminContent = file_get_contents(app_path('Http/Middleware/isAdmin.php'));
        
        // Verify it doesn't reference undefined 'dashboard' route
        $this->assertStringNotContainsString(
            "route('dashboard')",
            $isAdminContent,
            'isAdmin middleware should not reference undefined route(\'dashboard\')'
        );
        
        // Verify it correctly references user.dashboard
        $this->assertStringContainsString(
            "route('user.dashboard')",
            $isAdminContent,
            'isAdmin middleware should reference route(\'user.dashboard\') when redirecting users'
        );

        // Read the isUser middleware file
        $isUserContent = file_get_contents(app_path('Http/Middleware/isUser.php'));
        
        // Verify it doesn't reference undefined 'dashboard' route
        $this->assertStringNotContainsString(
            "route('dashboard')",
            $isUserContent,
            'isUser middleware should not reference undefined route(\'dashboard\')'
        );
        
        // Verify it correctly references admin.dashboard
        $this->assertStringContainsString(
            "route('admin.dashboard')",
            $isUserContent,
            'isUser middleware should reference route(\'admin.dashboard\') when redirecting admins'
        );
    }

    /**
     * Test that users are redirected to user.dashboard when trying to access admin routes
     */
    public function test_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('user.dashboard'));
        $response->assertSessionHas('error', 'Users cannot access admin pages.');
    }

    /**
     * Test that admins are redirected to admin.dashboard when trying to access user routes
     */
    public function test_admin_cannot_access_user_dashboard(): void
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $response = $this->get(route('user.dashboard'));

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('error', 'Admins cannot access user pages.');
    }

    /**
     * Test that unauthenticated users are redirected to login
     */
    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get(route('user.dashboard'));

        $response->assertRedirect(route('user.login'));
    }

    /**
     * Test that unauthenticated admins are redirected to admin login
     */
    public function test_unauthenticated_admin_redirected_to_admin_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }
}
