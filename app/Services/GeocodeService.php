<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GeocodeService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.geoapify.com/v1/geocode';

    public function __construct()
    {
        $this->apiKey = config('services.geoapify.api_key');
    }

    /**
     * Geocode an address to get latitude and longitude
     */
    public function geocodeAddress(string $address): ?array
    {
        if (empty($address)) {
            \Log::warning('Geocoding: Empty address provided');
            return null;
        }

        // Cache the result for 24 hours
        $cacheKey = 'geocode_' . md5($address);
        
        return Cache::remember($cacheKey, 86400, function () use ($address) {
            try {
                \Log::info('Geocoding address: ' . $address);
                \Log::info('API Key: ' . substr($this->apiKey, 0, 10) . '...');
                
                $response = Http::withOptions([
                    'verify' => false, // Disable SSL verification for development
                ])->get("{$this->baseUrl}/search", [
                    'text' => $address,
                    'apiKey' => $this->apiKey,
                    'limit' => 1
                ]);

                \Log::info('Geocoding response status: ' . $response->status());
                \Log::info('Geocoding response body: ' . $response->body());

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (!empty($data['features'])) {
                        $coordinates = $data['features'][0]['geometry']['coordinates'];
                        \Log::info('Geocoding successful: ' . json_encode($coordinates));
                        return [
                            'latitude' => $coordinates[1],
                            'longitude' => $coordinates[0]
                        ];
                    } else {
                        \Log::warning('Geocoding: No features found in response');
                    }
                } else {
                    \Log::error('Geocoding API error: ' . $response->body());
                }

                return null;
            } catch (\Exception $e) {
                \Log::error('Geocoding exception: ' . $e->getMessage());
                return null;
            }
        });
    }
}
