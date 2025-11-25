<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CalComService;
use Illuminate\Support\Facades\Config;

class CalComServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test configuration
        Config::set('services.calcom.api_key', 'test_api_key_12345');
        Config::set('services.calcom.base_url', 'https://api.cal.com/v1');
    }

    /**
     * Test CalComService can be instantiated with valid configuration
     */
    public function test_service_can_be_instantiated_with_valid_config(): void
    {
        $service = new CalComService();
        $this->assertInstanceOf(CalComService::class, $service);
    }

    /**
     * Test CalComService throws exception when API key is missing
     */
    public function test_service_throws_exception_when_api_key_missing(): void
    {
        Config::set('services.calcom.api_key', null);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cal.com API key is not configured');
        
        new CalComService();
    }

    /**
     * Test CalComService throws exception when API key is empty
     */
    public function test_service_throws_exception_when_api_key_empty(): void
    {
        Config::set('services.calcom.api_key', '');
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cal.com API key is not configured');
        
        new CalComService();
    }
}
