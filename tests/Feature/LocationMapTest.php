<?php

namespace Tests\Feature;

use App\Enums\LocationType;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationMapTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test location detail page with valid coordinates renders map container and correct data attributes.
     */
    public function test_location_detail_renders_map_with_valid_coordinates(): void
    {
        $user = User::factory()->operator()->create();
        $location = Location::factory()->create([
            'name' => 'Port of Rotterdam',
            'type' => LocationType::Port,
            'city' => 'Rotterdam',
            'country' => 'Netherlands',
            'latitude' => 51.9500300,
            'longitude' => 4.1333400,
        ]);

        $response = $this->actingAs($user)->get(route('locations.show', $location));

        $response->assertStatus(200);
        $response->assertSee('data-component="map"', false);
        $response->assertSee('51.95003', false);
        $response->assertSee('4.13334', false);
        $response->assertSee('https://www.openstreetmap.org/?mlat=51.95003&amp;mlon=4.13334#map=12/51.95003/4.13334', false);
        $response->assertSee('rel="noopener noreferrer"', false);
    }

    /**
     * Test location without coordinates displays friendly fallback message and does NOT render map container.
     */
    public function test_location_without_coordinates_displays_fallback_and_no_map(): void
    {
        $admin = User::factory()->admin()->create();
        $location = Location::factory()->create([
            'name' => 'Unmapped Transit Hub',
            'type' => LocationType::Warehouse,
            'city' => 'Frankfurt',
            'country' => 'Germany',
            'latitude' => null,
            'longitude' => null,
        ]);

        $response = $this->actingAs($admin)->get(route('locations.show', $location));

        $response->assertStatus(200);
        // Map container must NOT be rendered
        $response->assertDontSee('data-component="map"', false);
        // Fallback message must be present
        $response->assertSee(__('locations.map.no_coordinates'));
        $response->assertSee(__('locations.map.no_coordinates_desc'));
        // Link to edit form is shown to authorized user
        $response->assertSee(route('locations.edit', $location));
    }

    /**
     * Test location name containing HTML characters is safely escaped and does not inject raw executable HTML.
     */
    public function test_location_with_html_characters_prevents_xss(): void
    {
        $user = User::factory()->operator()->create();
        $maliciousName = '<script>alert("xss")</script> & "Special <Port>"';

        $location = Location::factory()->create([
            'name' => $maliciousName,
            'type' => LocationType::Port,
            'city' => 'Amsterdam',
            'country' => 'Netherlands',
            'latitude' => 52.3676,
            'longitude' => 4.9041,
        ]);

        $response = $this->actingAs($user)->get(route('locations.show', $location));

        $response->assertStatus(200);
        // Raw unescaped script tag must NOT exist in HTML
        $response->assertDontSee('<script>alert("xss")</script>', false);
        // It must be safely escaped in JSON data attribute
        $response->assertSee('\u003Cscript\u003Ealert(\u0022xss\u0022)\u003C\/script\u003E', false);
    }

    /**
     * Test create and edit forms render interactive map picker.
     */
    public function test_create_and_edit_forms_render_map_picker(): void
    {
        $admin = User::factory()->admin()->create();
        $location = Location::factory()->create([
            'latitude' => 1.290270,
            'longitude' => 103.851959,
        ]);

        // Create form
        $responseCreate = $this->actingAs($admin)->get(route('locations.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('data-component="map"', false);
        $responseCreate->assertSee('&quot;mode&quot;:&quot;picker&quot;', false);

        // Edit form
        $responseEdit = $this->actingAs($admin)->get(route('locations.edit', $location));
        $responseEdit->assertStatus(200);
        $responseEdit->assertSee('data-component="map"', false);
        $responseEdit->assertSee('&quot;mode&quot;:&quot;picker&quot;', false);
    }

    /**
     * Test index page renders summary multi-marker network map.
     */
    public function test_locations_index_renders_summary_network_map(): void
    {
        $operator = User::factory()->operator()->create();
        Location::factory()->count(3)->create([
            'latitude' => 51.5074,
            'longitude' => -0.1278,
        ]);

        $response = $this->actingAs($operator)->get(route('locations.index'));

        $response->assertStatus(200);
        $response->assertSee('data-component="map"', false);
        $response->assertSee('&quot;mode&quot;:&quot;multi&quot;', false);
        $response->assertSee(__('locations.map.summary_title'));
    }
}
