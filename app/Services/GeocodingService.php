<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeocodingService
{
    /**
     * Geocode a place/port/address query into WGS84 latitude and longitude coordinates.
     * Implements timeout, retries, caching, and graceful failure handling.
     *
     * @return array{
     *     success: bool,
     *     latitude: float|null,
     *     longitude: float|null,
     *     display_name: string|null,
     *     message: string|null
     * }
     */
    public function geocode(string $query): array
    {
        $normalizedQuery = trim($query);

        if (empty($normalizedQuery)) {
            return [
                'success' => false,
                'latitude' => null,
                'longitude' => null,
                'display_name' => null,
                'message' => 'Location search query cannot be empty.',
            ];
        }

        $cacheKey = 'geocode.'.md5(strtolower($normalizedQuery));
        $cacheTtl = config('services.geocoding.cache_ttl', 86400);

        return Cache::remember($cacheKey, $cacheTtl, function () use ($normalizedQuery) {
            $baseUrl = rtrim(config('services.geocoding.base_url', 'https://nominatim.openstreetmap.org'), '/');
            $timeout = (int) config('services.geocoding.timeout', 5);
            $retryTimes = (int) config('services.geocoding.retry_times', 2);
            $userAgent = config('services.geocoding.user_agent', 'CargoTrace/1.0 (contact@cargotrace.test)');

            try {
                $response = Http::timeout($timeout)
                    ->retry($retryTimes, 100)
                    ->withHeaders([
                        'User-Agent' => $userAgent,
                        'Accept' => 'application/json',
                    ])
                    ->get("{$baseUrl}/search", [
                        'q' => $normalizedQuery,
                        'format' => 'json',
                        'limit' => 1,
                    ]);

                if (! $response->successful()) {
                    Log::warning("Geocoding API responded with HTTP {$response->status()}", [
                        'query' => $normalizedQuery,
                        'body' => $response->body(),
                    ]);

                    return [
                        'success' => false,
                        'latitude' => null,
                        'longitude' => null,
                        'display_name' => null,
                        'message' => 'Geocoding service returned an unexpected response. Please enter coordinates manually.',
                    ];
                }

                $data = $response->json();

                if (! is_array($data) || empty($data)) {
                    return [
                        'success' => false,
                        'latitude' => null,
                        'longitude' => null,
                        'display_name' => null,
                        'message' => "No geographic coordinates found for '{$normalizedQuery}'.",
                    ];
                }

                $firstMatch = $data[0];

                if (! isset($firstMatch['lat'], $firstMatch['lon'])) {
                    return [
                        'success' => false,
                        'latitude' => null,
                        'longitude' => null,
                        'display_name' => null,
                        'message' => 'Invalid coordinate payload received from geocoding provider.',
                    ];
                }

                return [
                    'success' => true,
                    'latitude' => round((float) $firstMatch['lat'], 7),
                    'longitude' => round((float) $firstMatch['lon'], 7),
                    'display_name' => $firstMatch['display_name'] ?? $normalizedQuery,
                    'message' => 'Coordinates resolved successfully.',
                ];
            } catch (ConnectionException $e) {
                Log::warning('Geocoding service connection timed out.', [
                    'query' => $normalizedQuery,
                    'error' => $e->getMessage(),
                ]);

                return [
                    'success' => false,
                    'latitude' => null,
                    'longitude' => null,
                    'display_name' => null,
                    'message' => 'Geocoding provider connection timed out. Please enter coordinates manually.',
                ];
            } catch (RequestException|Throwable $e) {
                Log::error('Geocoding service exception encountered.', [
                    'query' => $normalizedQuery,
                    'error' => $e->getMessage(),
                ]);

                return [
                    'success' => false,
                    'latitude' => null,
                    'longitude' => null,
                    'display_name' => null,
                    'message' => 'Geocoding service temporarily unavailable. Please enter coordinates manually.',
                ];
            }
        });
    }
}
