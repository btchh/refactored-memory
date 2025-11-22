<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GeocodeService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.geoapify.com/v1/geocode';
    private int $cacheTime = 86400; // 24 hours

    public function __construct()
    {
        $this->apiKey = config('services.geoapify.api_key');
    }

    /**
     * Geocode an address to get latitude and longitude
     * 
     * @param string $address The address to geocode
     * @return array|null Array with latitude, longitude, and other data, or null if geocoding fails
     */
    public function geocodeAddress(string $address): ?array
    {
        if (empty($address) || !is_string($address)) {
            \Log::warning('Geocoding failed: Invalid address provided', [
                'address' => $address,
                'reason' => 'empty_or_invalid_type'
            ]);
            return null;
        }
        
        // Trim and validate address has meaningful content
        $address = trim($address);
        if (strlen($address) < 3) {
            \Log::warning('Geocoding failed: Address too short', [
                'address' => $address,
                'length' => strlen($address),
                'reason' => 'too_short'
            ]);
            return null;
        }

        // Cache the result
        $cacheKey = 'geocode_' . md5($address);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($address) {
            try {
                \Log::info('Geocoding address: ' . $address);
                
                // Generate multiple address variations
                $variations = $this->generateAddressVariations($address);
                
                \Log::info('Generated ' . count($variations) . ' address variations');
                foreach ($variations as $index => $variation) {
                    \Log::info("  Variation #{$index}: {$variation}");
                }
                
                // Try each variation and collect all results with scores
                $allResults = [];
                
                foreach ($variations as $variationIndex => $variation) {
                    \Log::info("Trying variation #{$variationIndex}: {$variation}");
                    
                    $results = $this->performGeocodeWithScoring($variation, $address);
                    
                    if (!empty($results)) {
                        foreach ($results as $result) {
                            $result['variation_index'] = $variationIndex;
                            $result['variation'] = $variation;
                            $allResults[] = $result;
                        }
                    }
                }
                
                if (empty($allResults)) {
                    \Log::warning('Geocoding failed: No results found for any variation', [
                        'address' => $address,
                        'variations_tried' => count($variations),
                        'reason' => 'no_results'
                    ]);
                    return null;
                }
                
                // Sort by total score descending
                usort($allResults, fn($a, $b) => $b['total_score'] <=> $a['total_score']);
                
                // Log top 3 results
                \Log::info('Top results:');
                foreach (array_slice($allResults, 0, 3) as $index => $result) {
                    \Log::info("  #{$index}: Score {$result['total_score']} - {$result['formatted_address']} (Variation: {$result['variation']})");
                }
                
                // Return the best result
                $best = $allResults[0];
                
                // Validate coordinates before returning
                if (!isset($best['latitude']) || !isset($best['longitude']) || 
                    !is_numeric($best['latitude']) || !is_numeric($best['longitude'])) {
                    \Log::error('Geocoding failed: Invalid coordinates in best result', [
                        'address' => $address,
                        'result' => $best,
                        'reason' => 'invalid_coordinates'
                    ]);
                    return null;
                }
                
                // Validate coordinate ranges
                $lat = (float) $best['latitude'];
                $lon = (float) $best['longitude'];
                if ($lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
                    \Log::error('Geocoding failed: Coordinates out of valid range', [
                        'address' => $address,
                        'latitude' => $lat,
                        'longitude' => $lon,
                        'reason' => 'out_of_range'
                    ]);
                    return null;
                }
                
                return [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'formatted_address' => $best['formatted_address'],
                    'result_type' => $best['result_type'],
                    'confidence' => $best['confidence'],
                    'total_score' => $best['total_score']
                ];
                
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                \Log::error('Geocoding failed: Network connection error', [
                    'address' => $address,
                    'error' => $e->getMessage(),
                    'reason' => 'network_error'
                ]);
                return null;
            } catch (\Illuminate\Http\Client\RequestException $e) {
                \Log::error('Geocoding failed: API request error', [
                    'address' => $address,
                    'error' => $e->getMessage(),
                    'status' => $e->response ? $e->response->status() : 'unknown',
                    'reason' => 'api_error'
                ]);
                return null;
            } catch (\Exception $e) {
                \Log::error('Geocoding failed: Unexpected exception', [
                    'address' => $address,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'reason' => 'exception'
                ]);
                return null;
            }
        });
    }

    /**
     * Generate address variations to improve geocoding accuracy
     */
    private function generateAddressVariations(string $address): array
    {
        $variations = [];
        
        // Clean and normalize
        $cleaned = trim($address);
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        
        // Normalize common abbreviations
        $cleaned = preg_replace('/\bbrgy\.?\s+/i', 'Barangay ', $cleaned);
        $cleaned = preg_replace('/\bbrg\.?\s+/i', 'Barangay ', $cleaned);
        
        // Parse address parts (split by comma)
        $parts = array_map('trim', explode(',', $cleaned));
        
        // Also try splitting by spaces if no commas
        if (count($parts) == 1) {
            $spaceParts = preg_split('/\s+/', $cleaned);
            // Try to identify province (usually last word or last 2 words)
            if (count($spaceParts) >= 3) {
                // Assume last word is province, second-to-last is municipality
                $barangay = implode(' ', array_slice($spaceParts, 0, -2));
                $municipality = $spaceParts[count($spaceParts) - 2];
                $province = $spaceParts[count($spaceParts) - 1];
                $parts = [$barangay, $municipality, $province];
            }
        }
        
        // Variation 1: Original address with Philippines
        $original = $cleaned;
        if (!str_contains(strtolower($original), 'philippines')) {
            $original .= ', Philippines';
        }
        $variations[] = $original;
        
        // If we have multiple parts, create variations
        if (count($parts) >= 2) {
            // Variation 2: Last 2 parts only (municipality + province) - MOST RELIABLE
            if (count($parts) >= 2) {
                $lastTwo = array_slice($parts, -2);
                $variation2 = implode(', ', $lastTwo);
                if (!str_contains(strtolower($variation2), 'philippines')) {
                    $variation2 .= ', Philippines';
                }
                $variations[] = $variation2;
            }
            
            // Variation 3: Last part only (province)
            $lastPart = $parts[count($parts) - 1];
            if (!str_contains(strtolower($lastPart), 'philippines')) {
                $lastPart .= ', Philippines';
            }
            $variations[] = $lastPart;
            
            // Variation 4: Remove first part (might be confusing barangay name)
            if (count($parts) >= 3) {
                $withoutFirst = array_slice($parts, 1);
                if (!empty($withoutFirst)) {
                    $variation4 = implode(', ', $withoutFirst);
                    if (!str_contains(strtolower($variation4), 'philippines')) {
                        $variation4 .= ', Philippines';
                    }
                    $variations[] = $variation4;
                }
            }
            
            // Variation 5: First part + last part (barangay + province, skip municipality)
            if (count($parts) >= 3) {
                $firstAndLast = [$parts[0], $parts[count($parts) - 1]];
                $variation5 = implode(', ', $firstAndLast);
                if (!str_contains(strtolower($variation5), 'philippines')) {
                    $variation5 .= ', Philippines';
                }
                $variations[] = $variation5;
            }
        }
        
        // For single-part addresses with multiple words
        if (count($parts) == 1 && str_word_count($cleaned) >= 3) {
            $words = preg_split('/\s+/', $cleaned);
            
            // Try last 2 words (likely municipality + province)
            if (count($words) >= 2) {
                $lastTwoWords = implode(' ', array_slice($words, -2));
                if (!str_contains(strtolower($lastTwoWords), 'philippines')) {
                    $lastTwoWords .= ', Philippines';
                }
                $variations[] = $lastTwoWords;
            }
            
            // Try last word only (likely province)
            $lastWord = $words[count($words) - 1];
            if (!str_contains(strtolower($lastWord), 'philippines')) {
                $lastWord .= ', Philippines';
            }
            $variations[] = $lastWord;
        }
        
        // Remove duplicates and empty values
        $variations = array_filter(array_unique($variations));
        
        return array_values($variations);
    }

    /**
     * Normalize Philippine addresses for better geocoding
     */
    private function normalizePhilippineAddress(string $address): string
    {
        $original = $address;
        
        // Convert to lowercase for processing
        $normalized = strtolower(trim($address));
        
        // Remove extra spaces and normalize separators
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        $normalized = preg_replace('/[,\.]+/', ',', $normalized);
        
        // Handle "Barangay" variations
        $normalized = preg_replace('/\bbrgy\.?\s+/i', 'barangay ', $normalized);
        $normalized = preg_replace('/\bbrg\.?\s+/i', 'barangay ', $normalized);
        
        // Parse Philippine address structure
        // Common patterns: "Barangay, Municipality, Province" or "Barangay Municipality Province"
        $parts = array_map('trim', explode(',', $normalized));
        
        // If we have multiple parts, try to identify municipality and province
        if (count($parts) >= 2) {
            // Last part should be province or "province, philippines"
            $lastPart = trim($parts[count($parts) - 1]);
            
            // Check if last part already has Philippines
            if (!str_contains($lastPart, 'philippines')) {
                // Add Philippines to the end
                $parts[count($parts) - 1] = $lastPart . ' philippines';
            }
            
            $normalized = implode(', ', $parts);
        } else {
            // Single part address - add Philippines
            if (!str_contains($normalized, 'philippines')) {
                $normalized .= ', philippines';
            }
        }
        
        // Clean up multiple commas
        $normalized = preg_replace('/,+/', ',', $normalized);
        $normalized = trim($normalized, ', ');
        
        // Capitalize each word for better matching
        $normalized = ucwords($normalized);
        
        \Log::info('Address normalization: "' . $original . '" -> "' . $normalized . '"');
        
        return $normalized;
    }

    /**
     * Perform geocoding with scoring for a single address variation
     */
    private function performGeocodeWithScoring(string $address, string $originalAddress): array
    {
        try {
            $response = Http::withOptions([
                'verify' => false,
            ])->timeout(10)->get("{$this->baseUrl}/search", [
                'text' => $address,
                'apiKey' => $this->apiKey,
                'limit' => 5,
                'filter' => 'countrycode:ph'
            ]);

            if (!$response->successful()) {
                \Log::warning("Geocoding API request failed", [
                    'address' => $address,
                    'status' => $response->status(),
                    'reason' => 'api_http_error'
                ]);
                return [];
            }

            $data = $response->json();
            
            \Log::info("API returned " . count($data['features'] ?? []) . " features for: {$address}");
            
            if (empty($data['features'])) {
                \Log::warning("No features found for: {$address}");
                return [];
            }

            $results = [];
            
            foreach ($data['features'] as $feature) {
                $props = $feature['properties'] ?? [];
                $geometry = $feature['geometry'] ?? [];
                $coordinates = $geometry['coordinates'] ?? null;
                
                if (!$coordinates || count($coordinates) < 2 || 
                    !is_numeric($coordinates[0]) || !is_numeric($coordinates[1])) {
                    \Log::warning("Invalid coordinates in feature");
                    continue;
                }
                
                // Validate coordinate ranges (lon, lat order in GeoJSON)
                $lon = (float) $coordinates[0];
                $lat = (float) $coordinates[1];
                if ($lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
                    \Log::warning("Coordinates out of valid range", ['lat' => $lat, 'lon' => $lon]);
                    continue;
                }
                
                $confidence = $props['rank']['confidence'] ?? 0;
                $resultType = $props['result_type'] ?? 'unknown';
                $formatted = $props['formatted'] ?? '';
                
                // Calculate scores
                $matchScore = $this->calculateMatchScore($formatted, $originalAddress);
                $typeBonus = $this->getResultTypeBonus($resultType);
                $totalScore = $confidence + $typeBonus + $matchScore;
                
                \Log::info("  Result: {$formatted} | Type: {$resultType} | Score: {$totalScore}");
                
                $results[] = [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'formatted_address' => $formatted,
                    'result_type' => $resultType,
                    'confidence' => $confidence,
                    'match_score' => $matchScore,
                    'type_bonus' => $typeBonus,
                    'total_score' => $totalScore
                ];
            }
            
            return $results;
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            \Log::error('Geocoding request failed: Network connection error', [
                'address' => $address,
                'error' => $e->getMessage(),
                'reason' => 'network_error'
            ]);
            return [];
        } catch (\Exception $e) {
            \Log::error('Geocoding request failed: Unexpected exception', [
                'address' => $address,
                'error' => $e->getMessage(),
                'reason' => 'exception'
            ]);
            return [];
        }
    }

    /**
     * Calculate match score based on how many terms from original address appear in result
     */
    private function calculateMatchScore(string $formatted, string $original): float
    {
        $formattedLower = strtolower($formatted);
        $originalLower = strtolower($original);
        
        // Extract meaningful terms (ignore common words)
        $ignoreWords = ['the', 'of', 'and', 'philippines', 'ph', 'barangay', 'brgy', 'city'];
        $originalTerms = preg_split('/[\s,]+/', $originalLower);
        $originalTerms = array_filter($originalTerms, fn($term) => 
            strlen($term) > 2 && !in_array($term, $ignoreWords)
        );
        
        $matchCount = 0;
        $exactMatches = 0;
        
        foreach ($originalTerms as $term) {
            if (str_contains($formattedLower, $term)) {
                $matchCount++;
                
                // Check for exact word match (not just substring)
                if (preg_match('/\b' . preg_quote($term, '/') . '\b/', $formattedLower)) {
                    $exactMatches++;
                }
            }
        }
        
        $totalTerms = count($originalTerms);
        if ($totalTerms == 0) {
            return 0;
        }
        
        // Base score from match ratio
        $baseScore = ($matchCount / $totalTerms) * 1.0;
        
        // Bonus for exact word matches (not just substrings)
        $exactBonus = ($exactMatches / $totalTerms) * 0.5;
        
        return $baseScore + $exactBonus;
    }

    /**
     * Get bonus score based on result type
     */
    private function getResultTypeBonus(string $resultType): float
    {
        $bonuses = [
            'amenity' => 0.4,      // Specific building/place
            'building' => 0.35,    // Specific building
            'street' => 0.3,       // Street level
            'postcode' => 0.3,     // Postcode level (specific area)
            'suburb' => 0.25,      // Barangay/suburb level
            'village' => 0.25,     // Village level
            'neighbourhood' => 0.2, // Neighborhood
            'locality' => 0.15,    // Town/city center
            'district' => 0.1,     // District
            'city' => 0.08,        // City level
            'county' => 0.05,      // County/municipality
            'state' => 0.02,       // Province level
            'country' => 0.01      // Country level (too broad)
        ];
        
        return $bonuses[$resultType] ?? 0;
    }

    /**
     * Select the best match from geocoding results
     */
    private function selectBestMatch(array $features, string $originalAddress): ?array
    {
        if (empty($features)) {
            return null;
        }

        // Scoring system for result types (higher is better)
        $typeScores = [
            'amenity' => 10,
            'building' => 9,
            'street' => 8,
            'locality' => 7,
            'district' => 6,
            'city' => 5,
            'county' => 4,
            'state' => 3,
            'country' => 1
        ];

        $scoredResults = [];
        
        foreach ($features as $feature) {
            $props = $feature['properties'] ?? [];
            $resultType = $props['result_type'] ?? 'unknown';
            $confidence = $props['rank']['confidence'] ?? 0;
            $formatted = strtolower($props['formatted'] ?? '');
            
            // Base score from result type
            $score = $typeScores[$resultType] ?? 2;
            
            // Boost score based on confidence
            $score += $confidence * 5;
            
            // Boost if the formatted address contains key parts of original address
            $addressLower = strtolower($originalAddress);
            $addressParts = preg_split('/[\s,]+/', $addressLower);
            
            foreach ($addressParts as $part) {
                if (strlen($part) > 3 && str_contains($formatted, $part)) {
                    $score += 2;
                }
            }
            
            // Prefer results in Batangas if address mentions it
            if (str_contains($addressLower, 'batangas') && str_contains($formatted, 'batangas')) {
                $score += 10;
            }
            
            // Prefer results in specified municipality
            if (str_contains($addressLower, 'rosario') && str_contains($formatted, 'rosario')) {
                $score += 8;
            }
            
            $scoredResults[] = [
                'feature' => $feature,
                'score' => $score,
                'formatted' => $props['formatted'] ?? 'Unknown'
            ];
        }

        // Sort by score descending
        usort($scoredResults, fn($a, $b) => $b['score'] <=> $a['score']);

        // Log scoring results
        \Log::info('Scored results:');
        foreach (array_slice($scoredResults, 0, 3) as $index => $result) {
            \Log::info("  #{$index}: Score {$result['score']} - {$result['formatted']}");
        }

        return $scoredResults[0]['feature'] ?? null;
    }

    /**
     * Try structured geocoding for Philippine addresses
     */
    private function tryStructuredGeocode(string $address): ?array
    {
        // Parse address into components
        $parts = array_map('trim', preg_split('/[,]+/', $address));
        
        if (count($parts) < 2) {
            return null;
        }

        // Try to identify components
        $params = [
            'apiKey' => $this->apiKey,
            'filter' => 'countrycode:ph',
            'limit' => 5
        ];

        // Last part is usually province
        if (count($parts) >= 3) {
            $params['state'] = $parts[count($parts) - 1];
            $params['city'] = $parts[count($parts) - 2];
            
            // First parts are barangay/street
            $streetParts = array_slice($parts, 0, count($parts) - 2);
            $params['street'] = implode(' ', $streetParts);
        } elseif (count($parts) == 2) {
            $params['city'] = $parts[1];
            $params['street'] = $parts[0];
        }

        \Log::info('Structured geocode params: ' . json_encode($params));

        try {
            $response = Http::withOptions([
                'verify' => false,
            ])->timeout(10)->get("{$this->baseUrl}/search", $params);

            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data['features'])) {
                    $bestMatch = $this->selectBestMatch($data['features'], $address);
                    
                    if ($bestMatch) {
                        $coordinates = $bestMatch['geometry']['coordinates'];
                        $properties = $bestMatch['properties'];
                        
                        \Log::info('Structured geocoding successful: ' . ($properties['formatted'] ?? 'Unknown'));
                        
                        return [
                            'latitude' => $coordinates[1],
                            'longitude' => $coordinates[0],
                            'formatted_address' => $properties['formatted'] ?? 'Unknown',
                            'result_type' => $properties['result_type'] ?? null,
                            'confidence' => $properties['rank']['confidence'] ?? null
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Structured geocoding exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Reverse geocode coordinates to get address
     */
    public function reverseGeocode(float $latitude, float $longitude): ?array
    {
        try {
            $response = Http::withOptions([
                'verify' => false,
            ])->timeout(10)->get("{$this->baseUrl}/reverse", [
                'lat' => $latitude,
                'lon' => $longitude,
                'apiKey' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data['features'])) {
                    $properties = $data['features'][0]['properties'];
                    
                    return [
                        'formatted_address' => $properties['formatted'] ?? null,
                        'city' => $properties['city'] ?? null,
                        'state' => $properties['state'] ?? null,
                        'country' => $properties['country'] ?? null,
                        'postcode' => $properties['postcode'] ?? null
                    ];
                }
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Reverse geocoding exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Clear geocode cache for a specific address
     */
    public function clearCache(string $address): void
    {
        $cacheKey = 'geocode_' . md5($address);
        Cache::forget($cacheKey);
        
        \Log::info('Cleared geocode cache for: ' . $address);
    }

    /**
     * Geocode without caching (for testing/debugging)
     */
    public function geocodeAddressFresh(string $address): ?array
    {
        // Clear cache first
        $this->clearCache($address);
        
        // Use the main geocoding method which will now cache the fresh result
        return $this->geocodeAddress($address);
    }
}
