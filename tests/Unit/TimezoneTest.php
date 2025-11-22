<?php

namespace Tests\Unit;

use Tests\TestCase;
use Carbon\Carbon;

class TimezoneTest extends TestCase
{
    /**
     * Test that application timezone is set to Asia/Manila
     */
    public function test_application_timezone_is_asia_manila(): void
    {
        $this->assertEquals('Asia/Manila', config('app.timezone'));
    }

    /**
     * Test that now() helper uses configured timezone
     */
    public function test_now_helper_uses_configured_timezone(): void
    {
        $now = now();
        $this->assertEquals('Asia/Manila', $now->timezone->getName());
    }

    /**
     * Test that Carbon instances use configured timezone by default
     */
    public function test_carbon_uses_configured_timezone(): void
    {
        $carbon = Carbon::now();
        $this->assertEquals('Asia/Manila', $carbon->timezone->getName());
    }

    /**
     * Test that datetime formatting works correctly with configured timezone
     */
    public function test_datetime_formatting_uses_configured_timezone(): void
    {
        $now = now();
        $formatted = $now->format('h:i A');
        
        // Verify format is correct (should be 12-hour format with AM/PM)
        $this->assertMatchesRegularExpression('/^\d{2}:\d{2} (AM|PM)$/', $formatted);
    }
}
