<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class WeatherService
{
    /**
     * Mandatory operational disclaimer to satisfy cargo analytics guidelines.
     */
    public const DISCLAIMER = 'Weather telemetry is provided for situational awareness only and should not be considered definitive evidence or a causal prediction of vessel delays.';

    /**
     * Fetch current meteorological conditions for the given coordinates.
     * Uses Open-Meteo (with fallback support for OpenWeather), caching, and resilient error handling.
     *
     * @return array{
     *     available: bool,
     *     temperature: float|null,
     *     temperature_unit: string,
     *     condition: string,
     *     weather_code: int|null,
     *     wind_speed: float|null,
     *     wind_speed_unit: string,
     *     humidity: int|null,
     *     humidity_unit: string,
     *     source: string,
     *     disclaimer: string,
     *     message: string|null,
     *     fetched_at: string
     * }
     */
    public function getWeather(float $latitude, float $longitude): array
    {
        $roundedLat = round($latitude, 3);
        $roundedLon = round($longitude, 3);
        $cacheKey = "weather.{$roundedLat}_{$roundedLon}";
        $cacheTtl = (int) config('services.weather.cache_ttl', 3600);

        return Cache::remember($cacheKey, $cacheTtl, function () use ($roundedLat, $roundedLon) {
            $provider = config('services.weather.provider', 'open-meteo');
            $timeout = (int) config('services.weather.timeout', 5);
            $retryTimes = (int) config('services.weather.retry_times', 2);

            try {
                if ($provider === 'openweather' && config('services.weather.openweather_api_key')) {
                    return $this->fetchOpenWeather($roundedLat, $roundedLon, $timeout, $retryTimes);
                }

                return $this->fetchOpenMeteo($roundedLat, $roundedLon, $timeout, $retryTimes);
            } catch (ConnectionException $e) {
                Log::warning('Weather telemetry connection timed out.', [
                    'lat' => $roundedLat,
                    'lon' => $roundedLon,
                    'error' => $e->getMessage(),
                ]);

                return $this->fallbackResponse('Meteorological satellite connection timed out.');
            } catch (RequestException|Throwable $e) {
                Log::warning('Weather service encountered an unexpected error.', [
                    'lat' => $roundedLat,
                    'lon' => $roundedLon,
                    'error' => $e->getMessage(),
                ]);

                return $this->fallbackResponse('Live weather telemetry currently offline.');
            }
        });
    }

    /**
     * Fetch conditions from Open-Meteo API.
     *
     * @return array<string, mixed>
     */
    protected function fetchOpenMeteo(float $lat, float $lon, int $timeout, int $retryTimes): array
    {
        $url = config('services.weather.open_meteo_url', 'https://api.open-meteo.com/v1/forecast');

        $response = Http::timeout($timeout)
            ->retry($retryTimes, 100)
            ->get($url, [
                'latitude' => $lat,
                'longitude' => $lon,
                'current' => 'temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m',
                'timezone' => 'auto',
            ]);

        if (! $response->successful()) {
            return $this->fallbackResponse("Weather provider returned HTTP {$response->status()}.");
        }

        $current = $response->json('current');

        if (! is_array($current) || ! isset($current['temperature_2m'])) {
            return $this->fallbackResponse('Incomplete meteorological payload received.');
        }

        $weatherCode = (int) ($current['weather_code'] ?? 0);
        $condition = $this->mapWeatherCodeToText($weatherCode);

        return [
            'available' => true,
            'temperature' => (float) $current['temperature_2m'],
            'temperature_unit' => '°C',
            'condition' => $condition,
            'weather_code' => $weatherCode,
            'wind_speed' => (float) ($current['wind_speed_10m'] ?? 0),
            'wind_speed_unit' => 'km/h',
            'humidity' => (int) ($current['relative_humidity_2m'] ?? 0),
            'humidity_unit' => '%',
            'source' => 'Open-Meteo Satellite Model',
            'disclaimer' => self::DISCLAIMER,
            'message' => null,
            'fetched_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Fetch conditions from OpenWeatherMap API.
     *
     * @return array<string, mixed>
     */
    protected function fetchOpenWeather(float $lat, float $lon, int $timeout, int $retryTimes): array
    {
        $url = config('services.weather.openweather_url', 'https://api.openweathermap.org/data/2.5/weather');
        $apiKey = config('services.weather.openweather_api_key');

        $response = Http::timeout($timeout)
            ->retry($retryTimes, 100)
            ->get($url, [
                'lat' => $lat,
                'lon' => $lon,
                'appid' => $apiKey,
                'units' => 'metric',
            ]);

        if (! $response->successful()) {
            return $this->fetchOpenMeteo($lat, $lon, $timeout, $retryTimes);
        }

        $data = $response->json();

        return [
            'available' => true,
            'temperature' => (float) ($data['main']['temp'] ?? 0),
            'temperature_unit' => '°C',
            'condition' => $data['weather'][0]['main'] ?? 'Clear',
            'weather_code' => (int) ($data['weather'][0]['id'] ?? 800),
            'wind_speed' => (float) ($data['wind']['speed'] ?? 0) * 3.6, // convert m/s to km/h
            'wind_speed_unit' => 'km/h',
            'humidity' => (int) ($data['main']['humidity'] ?? 0),
            'humidity_unit' => '%',
            'source' => 'OpenWeatherMap Global Service',
            'disclaimer' => self::DISCLAIMER,
            'message' => null,
            'fetched_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Standardized user-friendly fallback response when weather data cannot be retrieved.
     *
     * @return array<string, mixed>
     */
    protected function fallbackResponse(string $message): array
    {
        return [
            'available' => false,
            'temperature' => null,
            'temperature_unit' => '°C',
            'condition' => 'Telemetry Unavailable',
            'weather_code' => null,
            'wind_speed' => null,
            'wind_speed_unit' => 'km/h',
            'humidity' => null,
            'humidity_unit' => '%',
            'source' => 'Local Telemetry Sensor Cache',
            'disclaimer' => self::DISCLAIMER,
            'message' => $message,
            'fetched_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Map WMO Weather Interpretation Codes (WW) to human-readable maritime conditions.
     */
    public function mapWeatherCodeToText(int $code): string
    {
        return match (true) {
            $code === 0 => 'Clear Sky',
            in_array($code, [1, 2, 3], true) => 'Partly Cloudy / Fair',
            in_array($code, [45, 48], true) => 'Fog & Maritime Haze',
            in_array($code, [51, 53, 55], true) => 'Light Coastal Drizzle',
            in_array($code, [61, 63, 65], true) => 'Moderate to Heavy Rain',
            in_array($code, [71, 73, 75, 77], true) => 'Snow / Sleet Precipitation',
            in_array($code, [80, 81, 82], true) => 'Squall Rain Showers',
            in_array($code, [95, 96, 99], true) => 'Severe Thunderstorm Front',
            default => 'Fair Ocean Conditions',
        };
    }
}
