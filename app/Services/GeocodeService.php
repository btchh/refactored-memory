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
            // Try multiple address formats for better results
            $addressVariations = $this->generateAddressVariations($address);
            
            foreach ($addressVariations as $variation) {
                try {
                    \Log::info('Geocoding address variation: ' . $variation);
                    
                    $response = Http::withOptions([
                        'verify' => false, // Disable SSL verification for development
                    ])->get("{$this->baseUrl}/search", [
                        'text' => $variation,
                        'apiKey' => $this->apiKey,
                        'limit' => 1,
                        'filter' => 'countrycode:ph' // Filter to Philippines
                    ]);

                    \Log::info('Geocoding response status: ' . $response->status());

                    if ($response->successful()) {
                        $data = $response->json();
                        
                        if (!empty($data['features'])) {
                            $coordinates = $data['features'][0]['geometry']['coordinates'];
                            \Log::info('Geocoding successful with variation: ' . $variation);
                            \Log::info('Coordinates: ' . json_encode($coordinates));
                            return [
                                'latitude' => $coordinates[1],
                                'longitude' => $coordinates[0]
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Geocoding exception for variation: ' . $e->getMessage());
                    continue;
                }
            }
            
            \Log::warning('Geocoding: No results found for any address variation');
            return null;
        });
    }

    /**
     * Generate address variations for better geocoding results
     */
    private function generateAddressVariations(string $address): array
    {
        $variations = [];
        
        // Original address
        $variations[] = $address;
        
        // Extract city/municipality name (common pattern: ends with "City" or has province name)
        if (preg_match('/(.*?),?\s*(Lipa\s*City|Rosario|Batangas\s*City|Tanauan\s*City)/i', $address, $matches)) {
            $cityName = trim($matches[2]);
            
            // Just city name
            $variations[] = $cityName . ', Philippines';
            $variations[] = $cityName . ', Batangas, Philippines';
            
            // City with province
            if (stripos($cityName, 'City') !== false) {
                $variations[] = $cityName;
            }
        }
        
        // Extract street name if present
        if (preg_match('/([A-Z][a-z]+\s+(Street|St\.|Avenue|Ave\.|Road|Rd\.|Highway|Hwy\.))/i', $address, $matches)) {
            $streetName = trim($matches[0]);
            
            // Try street + city
            if (preg_match('/(Lipa|Rosario|Batangas|Tanauan)/i', $address, $cityMatch)) {
                $variations[] = $streetName . ', ' . $cityMatch[1] . ', Philippines';
            }
        }
        
        // Simplified version - remove lot/block numbers
        $simplified = preg_replace('/\b(B\d+\s*L\d+|Lot\s*\d+|Block\s*\d+)\s*/i', '', $address);
        if ($simplified !== $address) {
            $variations[] = trim($simplified);
        }
        
        // Remove extra details and keep only main location
        $parts = explode(',', $address);
        if (count($parts) > 1) {
            // Try last two parts (usually city and province)
            $variations[] = trim($parts[count($parts) - 1]) . ', Philippines';
            if (count($parts) > 2) {
                $variations[] = trim($parts[count($parts) - 2]) . ', ' . trim($parts[count($parts) - 1]) . ', Philippines';
            }
        }
        
        // Remove duplicates
        return array_unique($variations);
    }

    /**
     * Geocode all branches that don't have coordinates
     */
    public function geocodeAllBranches(): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
            'details' => []
        ];

        // Get all unique branch addresses without coordinates
        $branches = \App\Models\Admin::whereNotNull('branch_address')
            ->where('branch_address', '!=', '')
            ->where(function($query) {
                $query->whereNull('branch_latitude')
                      ->orWhereNull('branch_longitude');
            })
            ->get()
            ->groupBy('branch_address');

        foreach ($branches as $branchAddress => $adminsInBranch) {
            try {
                \Log::info("Attempting to geocode branch: {$branchAddress}");
                
                $coordinates = $this->geocodeAddress($branchAddress);
                
                if ($coordinates) {
                    // Update all admins at this branch
                    foreach ($adminsInBranch as $admin) {
                        $admin->update([
                            'branch_latitude' => $coordinates['latitude'],
                            'branch_longitude' => $coordinates['longitude'],
                            'location_updated_at' => now()
                        ]);
                    }
                    
                    $results['success']++;
                    $results['details'][] = [
                        'branch' => $branchAddress,
                        'status' => 'success',
                        'coordinates' => $coordinates,
                        'admins_updated' => $adminsInBranch->count()
                    ];
                    
                    \Log::info("Successfully geocoded: {$branchAddress}");
                } else {
                    $results['failed']++;
                    $results['details'][] = [
                        'branch' => $branchAddress,
                        'status' => 'failed',
                        'reason' => 'Geocoding returned no results'
                    ];
                    
                    \Log::warning("Failed to geocode: {$branchAddress}");
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['details'][] = [
                    'branch' => $branchAddress,
                    'status' => 'error',
                    'reason' => $e->getMessage()
                ];
                
                \Log::error("Error geocoding {$branchAddress}: " . $e->getMessage());
            }
        }

        return $results;
    }
}
