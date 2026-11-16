<?php

namespace Tests\Feature;

use App\Enums\DelayStatus;
use App\Enums\ShipmentStatus;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentFilterTest extends TestCase
{
    use RefreshDatabase;

    protected User $operator;

    protected User $customer1;

    protected User $customer2;

    protected Carrier $carrierA;

    protected Carrier $carrierB;

    protected Location $singapore;

    protected Location $rotterdam;

    protected Location $hamburg;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operator = User::factory()->operator()->create();
        $this->customer1 = User::factory()->customer()->create();
        $this->customer2 = User::factory()->customer()->create();

        $this->carrierA = Carrier::factory()->create(['name' => 'Maersk Line', 'code' => 'MSK']);
        $this->carrierB = Carrier::factory()->create(['name' => 'CMA CGM', 'code' => 'CMA']);

        $this->singapore = Location::factory()->create(['name' => 'Port of Singapore', 'city' => 'Singapore']);
        $this->rotterdam = Location::factory()->create(['name' => 'Port of Rotterdam', 'city' => 'Rotterdam']);
        $this->hamburg = Location::factory()->create(['name' => 'Port of Hamburg', 'city' => 'Hamburg']);
    }

    /**
     * Test searching by shipment number.
     */
    public function test_can_search_by_shipment_number(): void
    {
        $target = Shipment::factory()->create([
            'carrier_id' => $this->carrierA->id,
            'origin_location_id' => $this->singapore->id,
            'destination_location_id' => $this->rotterdam->id,
            'shipment_number' => 'CT-TARGET-9999',
            'cargo_type' => 'Industrial Machinery',
        ]);

        $other = Shipment::factory()->create([
            'carrier_id' => $this->carrierA->id,
            'origin_location_id' => $this->singapore->id,
            'destination_location_id' => $this->rotterdam->id,
            'shipment_number' => 'CT-OTHER-1111',
            'cargo_type' => 'Textiles',
        ]);

        $response = $this->actingAs($this->operator)->get(route('shipments.index', ['search' => 'TARGET-9999']));

        $response->assertStatus(200);
        $response->assertSee('CT-TARGET-9999');
        $response->assertDontSee('CT-OTHER-1111');
    }

    /**
     * Test searching by cargo type.
     */
    public function test_can_search_by_cargo_type(): void
    {
        Shipment::factory()->create([
            'carrier_id' => $this->carrierA->id,
            'origin_location_id' => $this->singapore->id,
            'destination_location_id' => $this->rotterdam->id,
            'shipment_number' => 'CT-ELECTRONICS-1',
            'cargo_type' => 'Semiconductor Wafers',
        ]);

        Shipment::factory()->create([
            'carrier_id' => $this->carrierA->id,
            'origin_location_id' => $this->singapore->id,
            'destination_location_id' => $this->rotterdam->id,
            'shipment_number' => 'CT-AGRICULTURE-1',
            'cargo_type' => 'Coffee Beans',
        ]);

        $response = $this->actingAs($this->operator)->get(route('shipments.index', ['search' => 'Semiconductor']));

        $response->assertStatus(200);
        $response->assertSee('CT-ELECTRONICS-1');
        $response->assertDontSee('CT-AGRICULTURE-1');
    }

    /**
     * Test filtering by lifecycle status and delay status simultaneously.
     */
    public function test_can_filter_by_status_and_delay_status(): void
    {
        $match = Shipment::factory()->create([
            'carrier_id' => $this->carrierA->id,
            'origin_location_id' => $this->singapore->id,
            'destination_location_id' => $this->rotterdam->id,
            'shipment_number' => 'CT-MATCH-001',
            'current_status' => ShipmentStatus::InTransit,
            'delay_status' => DelayStatus::High,
        ]);

        $wrongStatus = Shipment::factory()->create([
            'carrier_id' => $this->carrierA->id,
            'origin_location_id' => $this->singapore->id,
            'destination_location_id' => $this->rotterdam->id,
            'shipment_number' => 'CT-WRONG-STATUS',
            'current_status' => ShipmentStatus::Delivered,
            'delay_status' => DelayStatus::High,
        ]);

        $wrongDelay = Shipment::factory()->create([
            'carrier_id' => $this->carrierA->id,
            'origin_location_id' => $this->singapore->id,
            'destination_location_id' => $this->rotterdam->id,
            'shipment_number' => 'CT-WRONG-DELAY',
            'current_status' => ShipmentStatus::InTransit,
            'delay_status' => DelayStatus::OnTime,
        ]);

        $response = $this->actingAs($this->operator)->get(route('shipments.index', [
            'status' => 'IN_TRANSIT',
            'delay_status' => 'HIGH',
        ]));

        $response->assertStatus(200);
        $response->assertSee('CT-MATCH-001');
        $response->assertDontSee('CT-WRONG-STATUS');
        $response->assertDontSee('CT-WRONG-DELAY');
    }

    /**
     * Test filtering by carrier, origin, and destination locations.
     */
    public function test_can_filter_by_carrier_and_locations(): void
    {
        $match = Shipment::factory()->create([
            'carrier_id' => $this->carrierA->id,
            'origin_location_id' => $this->singapore->id,
            'destination_location_id' => $this->rotterdam->id,
            'shipment_number' => 'CT-CORRIDOR-MATCH',
        ]);

        $wrongCarrier = Shipment::factory()->create([
            'carrier_id' => $this->carrierB->id,
            'origin_location_id' => $this->singapore->id,
            'destination_location_id' => $this->rotterdam->id,
            'shipment_number' => 'CT-DIFF-CARRIER',
        ]);

        $wrongDestination = Shipment::factory()->create([
            'carrier_id' => $this->carrierA->id,
            'origin_location_id' => $this->singapore->id,
            'destination_location_id' => $this->hamburg->id,
            'shipment_number' => 'CT-DIFF-DEST',
        ]);

        $response = $this->actingAs($this->operator)->get(route('shipments.index', [
            'carrier_id' => $this->carrierA->id,
            'origin_id' => $this->singapore->id,
            'destination_id' => $this->rotterdam->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('CT-CORRIDOR-MATCH');
        $response->assertDontSee('CT-DIFF-CARRIER');
        $response->assertDontSee('CT-DIFF-DEST');
    }

    /**
     * Test filtering by expected departure date range.
     */
    public function test_can_filter_by_departure_date_range(): void
    {
        $inRange = Shipment::factory()->create([
            'carrier_id' => $this->carrierA->id,
            'origin_location_id' => $this->singapore->id,
            'destination_location_id' => $this->rotterdam->id,
            'shipment_number' => 'CT-IN-DATE-RANGE',
            'expected_departure_at' => '2026-06-15 10:00:00',
        ]);

        $tooEarly = Shipment::factory()->create([
            'carrier_id' => $this->carrierA->id,
            'origin_location_id' => $this->singapore->id,
            'destination_location_id' => $this->rotterdam->id,
            'shipment_number' => 'CT-TOO-EARLY',
            'expected_departure_at' => '2026-05-01 10:00:00',
        ]);

        $tooLate = Shipment::factory()->create([
            'carrier_id' => $this->carrierA->id,
            'origin_location_id' => $this->singapore->id,
            'destination_location_id' => $this->rotterdam->id,
            'shipment_number' => 'CT-TOO-LATE',
            'expected_departure_at' => '2026-07-20 10:00:00',
        ]);

        $response = $this->actingAs($this->operator)->get(route('shipments.index', [
            'date_from' => '2026-06-01',
            'date_to' => '2026-06-30',
        ]));

        $response->assertStatus(200);
        $response->assertSee('CT-IN-DATE-RANGE');
        $response->assertDontSee('CT-TOO-EARLY');
        $response->assertDontSee('CT-TOO-LATE');
    }

    /**
     * Test pagination preserves all filter queries via withQueryString.
     */
    public function test_pagination_preserves_query_string_parameters(): void
    {
        // Create 15 shipments for carrierA to trigger pagination (>10 per page)
        Shipment::factory()->count(15)->create([
            'carrier_id' => $this->carrierA->id,
            'origin_location_id' => $this->singapore->id,
            'destination_location_id' => $this->rotterdam->id,
        ]);

        $response = $this->actingAs($this->operator)->get(route('shipments.index', [
            'carrier_id' => $this->carrierA->id,
            'page' => 1,
        ]));

        $response->assertStatus(200);
        // Assert that the page 2 pagination link contains carrier_id
        $response->assertSee('carrier_id='.$this->carrierA->id);
    }

    /**
     * Test customer search and filter preserves ownership isolation.
     */
    public function test_customer_filtering_cannot_access_other_customers_cargo(): void
    {
        // Customer 1 cargo
        Shipment::factory()->create([
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrierA->id,
            'origin_location_id' => $this->singapore->id,
            'destination_location_id' => $this->rotterdam->id,
            'shipment_number' => 'CT-CUST1-CARGO',
            'cargo_type' => 'Medical Equipment',
        ]);

        // Customer 2 cargo with matching cargo type
        Shipment::factory()->create([
            'customer_id' => $this->customer2->id,
            'carrier_id' => $this->carrierA->id,
            'origin_location_id' => $this->singapore->id,
            'destination_location_id' => $this->rotterdam->id,
            'shipment_number' => 'CT-CUST2-SECRET-CARGO',
            'cargo_type' => 'Medical Equipment',
        ]);

        // Customer 1 searches for "Medical"
        $response = $this->actingAs($this->customer1)->get(route('shipments.index', [
            'search' => 'Medical',
        ]));

        $response->assertStatus(200);
        $response->assertSee('CT-CUST1-CARGO');
        $response->assertDontSee('CT-CUST2-SECRET-CARGO');
    }
}
