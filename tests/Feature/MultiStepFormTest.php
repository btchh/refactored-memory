<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;

/**
 * Multi-Step Form Integration Tests
 * 
 * Tests the multi-step form functionality for user registration and admin creation
 * Validates: Requirements 2.1, 2.2, 2.3, 2.4, 2.5
 */
class MultiStepFormTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration form renders correctly
     * Validates: Requirement 2.3 - Form step navigation
     */
    public function test_user_registration_form_renders_with_all_steps(): void
    {
        $response = $this->get(route('user.register'));

        $response->assertStatus(200);
        
        // Check that all three steps are present
        $response->assertSee('step1');
        $response->assertSee('step2');
        $response->assertSee('step3');
        
        // Check progress indicators
        $response->assertSee('step1-indicator');
        $response->assertSee('step2-indicator');
        $response->assertSee('step3-indicator');
        
        // Check forms exist
        $response->assertSee('contact-form');
        $response->assertSee('otp-form');
        $response->assertSee('registration-form');
        
        // Check error elements exist
        $response->assertSee('contact-error');
        $response->assertSee('otp-error');
    }

    /**
     * Test admin create form renders correctly (requires authentication)
     * Validates: Requirement 2.3 - Form step navigation
     */
    public function test_admin_create_form_renders_with_all_steps(): void
    {
        // Create and authenticate an admin
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $response = $this->get(route('admin.create-admin'));

        $response->assertStatus(200);
        
        // Check that all three steps are present
        $response->assertSee('step1');
        $response->assertSee('step2');
        $response->assertSee('step3');
        
        // Check progress indicators
        $response->assertSee('step1-indicator');
        $response->assertSee('step2-indicator');
        $response->assertSee('step3-indicator');
        
        // Check forms exist
        $response->assertSee('contact-form');
        $response->assertSee('otp-form');
        $response->assertSee('Admin Details'); // Check for step 3 content
        
        // Check error elements exist
        $response->assertSee('contact-error');
        $response->assertSee('otp-error');
    }

    /**
     * Test user registration OTP send endpoint
     * Validates: Requirement 2.1 - OTP sending functionality
     */
    public function test_user_registration_send_otp_endpoint(): void
    {
        $response = $this->postJson(route('user.send-registration-otp'), [
            'email' => 'test@example.com',
            'phone' => '09123456789'
        ]);

        // Should return success or appropriate response
        $response->assertStatus(200);
        $this->assertTrue(
            $response->json('success') === true || 
            isset($response->json()['message'])
        );
    }

    /**
     * Test user registration OTP send with missing data
     * Validates: Requirement 2.4 - Error message display
     */
    public function test_user_registration_send_otp_validation_error(): void
    {
        $response = $this->postJson(route('user.send-registration-otp'), [
            'email' => '',
            'phone' => ''
        ]);

        // Should return validation error
        $response->assertStatus(422);
    }

    /**
     * Test user registration OTP verify endpoint
     * Validates: Requirement 2.2 - OTP verification functionality
     * Validates: Requirement 2.4 - Error message display for invalid OTP
     */
    public function test_user_registration_verify_otp_endpoint(): void
    {
        $response = $this->postJson(route('user.verify-otp'), [
            'phone' => '09123456789',
            'otp' => '123456'
        ]);

        // Should return a response (success=false for invalid OTP is expected)
        $this->assertTrue(
            $response->status() === 200 || $response->status() === 400
        );
        $this->assertArrayHasKey('success', $response->json());
        
        // For invalid OTP, should have error message
        if ($response->status() === 400) {
            $this->assertFalse($response->json('success'));
            $this->assertArrayHasKey('message', $response->json());
        }
    }

    /**
     * Test admin OTP send endpoint
     * Validates: Requirement 2.1 - OTP sending functionality
     */
    public function test_admin_send_otp_endpoint(): void
    {
        // Create and authenticate an admin
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $response = $this->postJson(route('admin.send-admin-otp'), [
            'email' => 'newadmin@example.com',
            'phone' => '09123456789'
        ]);

        // Should return success or appropriate response
        $response->assertStatus(200);
        $this->assertTrue(
            $response->json('success') === true || 
            isset($response->json()['message'])
        );
    }

    /**
     * Test admin OTP send with missing data
     * Validates: Requirement 2.4 - Error message display
     */
    public function test_admin_send_otp_validation_error(): void
    {
        // Create and authenticate an admin
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $response = $this->postJson(route('admin.send-admin-otp'), [
            'email' => '',
            'phone' => ''
        ]);

        // Should return validation error
        $response->assertStatus(422);
    }

    /**
     * Test admin OTP verify endpoint
     * Validates: Requirement 2.2 - OTP verification functionality
     * Validates: Requirement 2.4 - Error message display for invalid OTP
     */
    public function test_admin_verify_otp_endpoint(): void
    {
        // Create and authenticate an admin
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $response = $this->postJson(route('admin.verify-admin-otp'), [
            'phone' => '09123456789',
            'otp' => '123456'
        ]);

        // Should return a response (success=false for invalid OTP is expected)
        $this->assertTrue(
            $response->status() === 200 || $response->status() === 400
        );
        $this->assertArrayHasKey('success', $response->json());
        
        // For invalid OTP, should have error message
        if ($response->status() === 400) {
            $this->assertFalse($response->json('success'));
            $this->assertArrayHasKey('message', $response->json());
        }
    }

    /**
     * Test that JavaScript files are loaded on registration page
     * Validates: Requirement 2.3 - Form functionality
     */
    public function test_user_registration_loads_javascript(): void
    {
        $response = $this->get(route('user.register'));

        $response->assertStatus(200);
        
        // Check that data attributes for JavaScript are present
        $response->assertSee('data-send-otp-url');
        $response->assertSee('data-verify-otp-url');
    }

    /**
     * Test that JavaScript files are loaded on admin create page
     * Validates: Requirement 2.3 - Form functionality
     */
    public function test_admin_create_loads_javascript(): void
    {
        // Create and authenticate an admin
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $response = $this->get(route('admin.create-admin'));

        $response->assertStatus(200);
        
        // Check that data attributes for JavaScript are present
        $response->assertSee('data-send-otp-url');
        $response->assertSee('data-verify-otp-url');
    }

    /**
     * Test button elements exist on user registration form
     * Validates: Requirement 2.5 - Button state management
     */
    public function test_user_registration_has_required_buttons(): void
    {
        $response = $this->get(route('user.register'));

        $response->assertStatus(200);
        
        // Check for buttons in each step
        $response->assertSee('Send OTP');
        $response->assertSee('Verify OTP');
        $response->assertSee('Create Account');
        $response->assertSee('Back');
    }

    /**
     * Test button elements exist on admin create form
     * Validates: Requirement 2.5 - Button state management
     */
    public function test_admin_create_has_required_buttons(): void
    {
        // Create and authenticate an admin
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $response = $this->get(route('admin.create-admin'));

        $response->assertStatus(200);
        
        // Check for buttons in each step
        $response->assertSee('Send OTP');
        $response->assertSee('Verify OTP');
        $response->assertSee('Create Admin');
        $response->assertSee('Back');
    }
}
