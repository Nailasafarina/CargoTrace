<?php

namespace Tests\Feature;

use App\Enums\LocationType;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataPolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that an operator can view carriers and locations.
     */
    public function test_operator_can_view_carriers_and_locations(): void
    {
        $operator = User::factory()->operator()->create();
        $carrier = Carrier::factory()->create();
        $location = Location::factory()->create();

        // Index
        $this->actingAs($operator)->get('/carriers')->assertStatus(200)->assertSee($carrier->name);
        $this->actingAs($operator)->get('/locations')->assertStatus(200)->assertSee($location->name);

        // Show
        $this->actingAs($operator)->get("/carriers/{$carrier->id}")->assertStatus(200)->assertSee($carrier->name);
        $this->actingAs($operator)->get("/locations/{$location->id}")->assertStatus(200)->assertSee($location->name);
    }

    /**
     * Test that an operator cannot create, update, or delete a carrier (403 Forbidden).
     */
    public function test_operator_cannot_create_update_delete_carrier(): void
    {
        $operator = User::factory()->operator()->create();
        $carrier = Carrier::factory()->create();

        // Create
        $this->actingAs($operator)->get('/carriers/create')->assertStatus(403);
        $this->actingAs($operator)->post('/carriers', [
            'name' => 'Unauthorized Line',
            'code' => 'UNAUTH',
        ])->assertStatus(403);

        // Update
        $this->actingAs($operator)->get("/carriers/{$carrier->id}/edit")->assertStatus(403);
        $this->actingAs($operator)->put("/carriers/{$carrier->id}", [
            'name' => 'Altered Line',
            'code' => $carrier->code,
        ])->assertStatus(403);

        // Delete
        $this->actingAs($operator)->delete("/carriers/{$carrier->id}")->assertStatus(403);
    }

    /**
     * Test that an operator cannot create, update, or delete a location (403 Forbidden).
     */
    public function test_operator_cannot_create_update_delete_location(): void
    {
        $operator = User::factory()->operator()->create();
        $location = Location::factory()->create();

        // Create
        $this->actingAs($operator)->get('/locations/create')->assertStatus(403);
        $this->actingAs($operator)->post('/locations', [
            'name' => 'Unauthorized Port',
            'type' => LocationType::Port->value,
            'city' => 'Unknown',
            'country' => 'Unknown',
            'latitude' => 0.0,
            'longitude' => 0.0,
        ])->assertStatus(403);

        // Update
        $this->actingAs($operator)->get("/locations/{$location->id}/edit")->assertStatus(403);
        $this->actingAs($operator)->put("/locations/{$location->id}", [
            'name' => 'Altered Port',
            'type' => LocationType::Port->value,
            'city' => 'Altered',
            'country' => 'Altered',
            'latitude' => 10.0,
            'longitude' => 10.0,
        ])->assertStatus(403);

        // Delete
        $this->actingAs($operator)->delete("/locations/{$location->id}")->assertStatus(403);
    }

    /**
     * Test that an admin can create, update, and delete carriers.
     */
    public function test_admin_can_create_update_delete_carrier(): void
    {
        $admin = User::factory()->admin()->create();

        // Create
        $response = $this->actingAs($admin)->post('/carriers', [
            'name' => 'Zim Integrated Shipping',
            'code' => 'ZIM',
            'contact_email' => 'ops@zim.com',
            'contact_phone' => '+972 4 8652111',
            'is_active' => true,
        ]);
        $response->assertRedirect('/carriers');
        $this->assertDatabaseHas('carriers', ['code' => 'ZIM']);

        $carrier = Carrier::where('code', 'ZIM')->first();

        // Update
        $response = $this->actingAs($admin)->put("/carriers/{$carrier->id}", [
            'name' => 'ZIM Integrated Shipping Services Ltd',
            'code' => 'ZIM',
            'is_active' => true,
        ]);
        $response->assertRedirect('/carriers');
        $this->assertDatabaseHas('carriers', ['name' => 'ZIM Integrated Shipping Services Ltd']);

        // Delete
        $response = $this->actingAs($admin)->delete("/carriers/{$carrier->id}");
        $response->assertRedirect('/carriers');
        $this->assertDatabaseMissing('carriers', ['id' => $carrier->id]);
    }

    /**
     * Test that an admin can create, update, and delete locations.
     */
    public function test_admin_can_create_update_delete_location(): void
    {
        $admin = User::factory()->admin()->create();

        // Create
        $response = $this->actingAs($admin)->post('/locations', [
            'name' => 'Port of Genoa',
            'type' => LocationType::Port->value,
            'city' => 'Genoa',
            'country' => 'Italy',
            'latitude' => 44.4056,
            'longitude' => 8.9463,
        ]);
        $response->assertRedirect('/locations');
        $this->assertDatabaseHas('locations', ['name' => 'Port of Genoa']);

        $location = Location::where('name', 'Port of Genoa')->first();

        // Update
        $response = $this->actingAs($admin)->put("/locations/{$location->id}", [
            'name' => 'Port of Genoa West Terminal',
            'type' => LocationType::Port->value,
            'city' => 'Genoa',
            'country' => 'Italy',
            'latitude' => 44.4100,
            'longitude' => 8.9500,
        ]);
        $response->assertRedirect('/locations');
        $this->assertDatabaseHas('locations', ['name' => 'Port of Genoa West Terminal']);

        // Delete
        $response = $this->actingAs($admin)->delete("/locations/{$location->id}");
        $response->assertRedirect('/locations');
        $this->assertDatabaseMissing('locations', ['id' => $location->id]);
    }
}
