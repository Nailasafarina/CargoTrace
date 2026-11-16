<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use App\Services\GeocodingService;
use App\Services\WeatherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExternalServicesTest extends TestCase
{
    use RefreshDatabase;

    protected GeocodingService $geocodingService;

    protected WeatherService $weatherService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->geocodingService = app(GeocodingService::class);
        $this->weatherService = app(WeatherService::class);
        Cache::flush();
    }

    /**
     * Test GeocodingService successfully resolves coordinates from provider.
     */
    public function test_geocoding_service_success_resolves_coordinates(): void
    {
        Http::fake([
            '*/search*' => Http::response([
                [
                    'place_id' => 12345,
                    'lat' => '-6.1018000',
                    'lon' => '106.8833000',
                    'display_name' => 'Port of Tanjung Priok, North Jakarta, Indonesia',
                ],
            ], 200),
        ]);

        $result = $this->geocodingService->geocode('Port of Tanjung Priok');

        $this->assertTrue($result['success']);
        $this->assertEquals(-6.1018, $result['latitude']);
        $this->assertEquals(106.8833, $result['longitude']);
        $this->assertStringContainsString('Tanjung Priok', $result['display_name']);
    }

    /**
     * Test GeocodingService handles empty match responses gracefully.
     */
    public function test_geocoding_service_handles_empty_response(): void
    {
        Http::fake([
            '*/search*' => Http::response([], 200),
        ]);

        $result = $this->geocodingService->geocode('NonExistentPortXYZ999');

        $this->assertFalse($result['success']);
        $this->assertNull($result['latitude']);
        $this->assertNull($result['longitude']);
        $this->assertStringContainsString('No geographic coordinates found', $result['message']);
    }

    /**
     * Test GeocodingService handles HTTP 500 server errors gracefully without throwing unhandled exceptions.
     */
    public function test_geocoding_service_handles_500_server_error(): void
    {
        Http::fake([
            '*/search*' => Http::response('Internal Server Error', 500),
        ]);

        $result = $this->geocodingService->geocode('Port of Hamburg');

        $this->assertFalse($result['success']);
        $this->assertNull($result['latitude']);
        $this->assertStringContainsString('manually', $result['message']);
    }

    /**
     * Test GeocodingService handles network connection timeouts gracefully.
     */
    public function test_geocoding_service_handles_timeout_gracefully(): void
    {
        Http::fake([
            '*/search*' => fn () => throw new ConnectionException('cURL error 28: Operation timed out after 5000 milliseconds'),
        ]);

        $result = $this->geocodingService->geocode('Port of Antwerp');

        $this->assertFalse($result['success']);
        $this->assertNull($result['latitude']);
        $this->assertStringContainsString('timed out', $result['message']);
    }

    /**
     * Test GeocodingService caches results to conserve network requests.
     */
    public function test_geocoding_service_caches_results(): void
    {
        Http::fake([
            '*/search*' => Http::sequence()
                ->push([['lat' => '51.9536000', 'lon' => '4.1333000', 'display_name' => 'Rotterdam Port']], 200)
                ->push([], 500), // Second request would fail if not cached
        ]);

        $result1 = $this->geocodingService->geocode('Rotterdam');
        $this->assertTrue($result1['success']);

        $result2 = $this->geocodingService->geocode('Rotterdam');
        $this->assertTrue($result2['success']);
        $this->assertEquals(51.9536, $result2['latitude']);
    }

    /**
     * Test WeatherService successfully resolves meteorological conditions.
     */
    public function test_weather_service_success_returns_live_conditions(): void
    {
        Http::fake([
            '*/forecast*' => Http::response([
                'current' => [
                    'temperature_2m' => 28.5,
                    'relative_humidity_2m' => 76,
                    'weather_code' => 1,
                    'wind_speed_10m' => 15.4,
                ],
            ], 200),
        ]);

        $result = $this->weatherService->getWeather(-6.1018, 106.8833);

        $this->assertTrue($result['available']);
        $this->assertEquals(28.5, $result['temperature']);
        $this->assertEquals('Partly Cloudy / Fair', $result['condition']);
        $this->assertEquals(15.4, $result['wind_speed']);
        $this->assertEquals(76, $result['humidity']);
        $this->assertEquals(WeatherService::DISCLAIMER, $result['disclaimer']);
    }

    /**
     * Test WeatherService handles empty or malformed payload without crashing.
     */
    public function test_weather_service_handles_empty_or_malformed_payload(): void
    {
        Http::fake([
            '*/forecast*' => Http::response(['current' => null], 200),
        ]);

        $result = $this->weatherService->getWeather(51.95, 4.13);

        $this->assertFalse($result['available']);
        $this->assertNull($result['temperature']);
        $this->assertEquals('Telemetry Unavailable', $result['condition']);
        $this->assertEquals(WeatherService::DISCLAIMER, $result['disclaimer']);
    }

    /**
     * Test WeatherService handles HTTP 500 error from weather provider.
     */
    public function test_weather_service_handles_500_error_gracefully(): void
    {
        Http::fake([
            '*/forecast*' => Http::response('Server Down', 500),
        ]);

        $result = $this->weatherService->getWeather(1.264, 103.84);

        $this->assertFalse($result['available']);
        $this->assertNull($result['temperature']);
        $this->assertEquals(WeatherService::DISCLAIMER, $result['disclaimer']);
    }

    /**
     * Test WeatherService handles connection timeouts gracefully.
     */
    public function test_weather_service_handles_timeout_gracefully(): void
    {
        Http::fake([
            '*/forecast*' => fn () => throw new ConnectionException('Weather provider connection timeout'),
        ]);

        $result = $this->weatherService->getWeather(6.949, 79.842);

        $this->assertFalse($result['available']);
        $this->assertStringContainsString('timed out', $result['message']);
        $this->assertEquals(WeatherService::DISCLAIMER, $result['disclaimer']);
    }

    /**
     * Test WeatherService caches telemetry data.
     */
    public function test_weather_service_caches_results(): void
    {
        Http::fake([
            '*/forecast*' => Http::sequence()
                ->push(['current' => ['temperature_2m' => 22.0, 'weather_code' => 0]], 200)
                ->push([], 500),
        ]);

        $first = $this->weatherService->getWeather(51.95, 4.13);
        $this->assertTrue($first['available']);

        $second = $this->weatherService->getWeather(51.95, 4.13);
        $this->assertTrue($second['available']);
        $this->assertEquals(22.0, $second['temperature']);
    }

    /**
     * Test Location Geocode endpoint resolves coordinates for authorized Admin.
     */
    public function test_admin_can_resolve_location_geocode_endpoint(): void
    {
        Http::fake([
            '*/search*' => Http::response([
                [
                    'lat' => '51.9536000',
                    'lon' => '4.1333000',
                    'display_name' => 'Port of Rotterdam, Netherlands',
                ],
            ], 200),
        ]);

        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->postJson(route('locations.geocode'), [
            'query' => 'Port of Rotterdam',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'latitude' => 51.9536,
                'longitude' => 4.1333,
            ]);
    }

    /**
     * Test Customer cannot access Location Geocode endpoint (HTTP 403).
     */
    public function test_customer_cannot_access_location_geocode_endpoint(): void
    {
        $customer = User::factory()->customer()->create();

        $response = $this->actingAs($customer)->postJson(route('locations.geocode'), [
            'query' => 'Port of Rotterdam',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test Shipment show page renders Leaflet map, OpenStreetMap attribution, and weather disclaimer.
     */
    public function test_shipment_show_page_renders_map_and_osm_attribution(): void
    {
        Http::fake([
            '*/forecast*' => Http::response([
                'current' => [
                    'temperature_2m' => 29.0,
                    'relative_humidity_2m' => 70,
                    'weather_code' => 0,
                    'wind_speed_10m' => 11.0,
                ],
            ], 200),
        ]);

        $operator = User::factory()->operator()->create();
        $carrier = Carrier::factory()->create();
        $origin = Location::factory()->create([
            'name' => 'Port of Tanjung Priok',
            'latitude' => -6.1018,
            'longitude' => 106.8833,
        ]);
        $destination = Location::factory()->create([
            'name' => 'Port of Rotterdam',
            'latitude' => 51.9536,
            'longitude' => 4.1333,
        ]);

        $shipment = Shipment::factory()->create([
            'customer_id' => User::factory()->customer()->create()->id,
            'carrier_id' => $carrier->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
        ]);

        $response = $this->actingAs($operator)->get(route('shipments.show', $shipment));

        $response->assertStatus(200);
        $response->assertSee('Interactive Global Corridor');
        $response->assertSee('Telemetry Map');
        $response->assertSee('OpenStreetMap Tiles');
        $response->assertSee('id="shipment-corridor-map"', false);
        $response->assertSee('Maritime Context Disclaimer');
        $response->assertSee(WeatherService::DISCLAIMER);
    }

    /**
     * Test Shipment show page handles shipments without GPS coordinates cleanly.
     */
    public function test_shipment_show_page_handles_missing_coordinates_gracefully(): void
    {
        $operator = User::factory()->operator()->create();
        $carrier = Carrier::factory()->create();
        $origin = Location::factory()->create(['latitude' => null, 'longitude' => null]);
        $destination = Location::factory()->create(['latitude' => null, 'longitude' => null]);

        $shipment = Shipment::factory()->create([
            'carrier_id' => $carrier->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
        ]);

        $response = $this->actingAs($operator)->get(route('shipments.show', $shipment));

        $response->assertStatus(200);
        $response->assertSee('Geographic Coordinates Unavailable');
        $response->assertDontSee('id="shipment-corridor-map"', false);
    }
}
