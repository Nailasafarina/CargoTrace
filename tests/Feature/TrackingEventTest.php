<?php

namespace Tests\Feature;

use App\Enums\ShipmentStatus;
use App\Enums\TrackingEventType;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingEventTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper to create a realistic shipment along the Southeast Asia -> Europe corridor.
     */
    protected function createCorridorShipment(
        ?User $operator = null,
        ?User $customer = null,
        ShipmentStatus $status = ShipmentStatus::Booked
    ): Shipment {
        $operator = $operator ?? User::factory()->operator()->create();
        $customer = $customer ?? User::factory()->customer()->create();
        $carrier = Carrier::factory()->create();
        $origin = Location::factory()->create([
            'name' => 'Port of Tanjung Priok',
            'latitude' => -6.1018000,
            'longitude' => 106.8833000,
        ]);
        $destination = Location::factory()->create([
            'name' => 'Port of Rotterdam',
            'latitude' => 51.9536000,
            'longitude' => 4.1333000,
        ]);

        return Shipment::factory()->create([
            'customer_id' => $customer->id,
            'carrier_id' => $carrier->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
            'current_status' => $status,
            'created_by' => $operator->id,
        ]);
    }

    /**
     * Test valid status transition and event append by an operator.
     */
    public function test_operator_can_add_valid_tracking_event_and_transition_status(): void
    {
        $operator = User::factory()->operator()->create();
        $customer = User::factory()->customer()->create();
        $shipment = $this->createCorridorShipment($operator, $customer, ShipmentStatus::Booked);
        $location = Location::factory()->create(['name' => 'Port of Singapore', 'latitude' => 1.2644, 'longitude' => 103.8400]);

        $payload = [
            'status' => ShipmentStatus::PickedUp->value,
            'event_type' => TrackingEventType::PickedUp->value,
            'location_id' => $location->id,
            'occurred_at' => Carbon::now()->subMinutes(10)->format('Y-m-d H:i:s'),
            'latitude' => 1.2644,
            'longitude' => 103.8400,
            'description' => 'Container sealed and loaded onto feeder vessel at Singapore feeder terminal.',
            'is_exception' => 0,
        ];

        $response = $this->actingAs($operator)->post("/shipments/{$shipment->id}/events", $payload);

        $response->assertRedirect(route('shipments.show', $shipment));
        $response->assertSessionHas('status');

        // Verify shipment status updated
        $this->assertEquals(ShipmentStatus::PickedUp, $shipment->fresh()->current_status);

        // Verify tracking event persisted
        $this->assertDatabaseHas('tracking_events', [
            'shipment_id' => $shipment->id,
            'location_id' => $location->id,
            'event_type' => TrackingEventType::PickedUp->value,
            'created_by' => $operator->id,
            'is_exception' => false,
        ]);

        // Verify audit log generated
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $operator->id,
            'action' => 'TRACKING_EVENT_ADDED',
            'entity_type' => Shipment::class,
            'entity_id' => $shipment->id,
        ]);

        // Verify in-app customer notification generated
        $this->assertDatabaseHas('notifications', [
            'user_id' => $customer->id,
            'shipment_id' => $shipment->id,
            'type' => 'SHIPMENT_MILESTONE_UPDATED',
        ]);
    }

    /**
     * Test administrator can also record valid tracking events.
     */
    public function test_admin_can_add_valid_tracking_event(): void
    {
        $admin = User::factory()->admin()->create();
        $shipment = $this->createCorridorShipment(status: ShipmentStatus::PickedUp);

        $payload = [
            'status' => ShipmentStatus::InTransit->value,
            'event_type' => TrackingEventType::InTransit->value,
            'occurred_at' => Carbon::now()->subMinutes(5)->format('Y-m-d H:i:s'),
            'description' => 'Vessel departed Singapore transshipment hub bound for Suez Canal.',
        ];

        $response = $this->actingAs($admin)->post("/shipments/{$shipment->id}/events", $payload);

        $response->assertRedirect(route('shipments.show', $shipment));
        $this->assertEquals(ShipmentStatus::InTransit, $shipment->fresh()->current_status);
    }

    /**
     * Test invalid status transition (skipping state or invalid flow) is rejected with HTTP 422.
     */
    public function test_invalid_status_transition_is_rejected_with_422(): void
    {
        $operator = User::factory()->operator()->create();

        // 1. Cannot skip directly from CREATED to IN_TRANSIT or DELIVERED
        $shipmentCreated = $this->createCorridorShipment(operator: $operator, status: ShipmentStatus::Created);

        $invalidPayload = [
            'status' => ShipmentStatus::Delivered->value,
            'occurred_at' => Carbon::now()->subMinutes(1)->format('Y-m-d H:i:s'),
            'description' => 'Attempting illicit bypass transition directly to DELIVERED.',
        ];

        $response = $this->actingAs($operator)->post("/shipments/{$shipmentCreated->id}/events", $invalidPayload);
        $response->assertStatus(422);
        $this->assertEquals(ShipmentStatus::Created, $shipmentCreated->fresh()->current_status);

        // 2. Cannot transition backwards from IN_TRANSIT to CREATED
        $shipmentInTransit = $this->createCorridorShipment(operator: $operator, status: ShipmentStatus::InTransit);

        $backwardPayload = [
            'status' => ShipmentStatus::Created->value,
            'occurred_at' => Carbon::now()->subMinutes(1)->format('Y-m-d H:i:s'),
            'description' => 'Attempting illicit backward transition to CREATED.',
        ];

        $responseBackward = $this->actingAs($operator)->post("/shipments/{$shipmentInTransit->id}/events", $backwardPayload);
        $responseBackward->assertStatus(422);
        $this->assertEquals(ShipmentStatus::InTransit, $shipmentInTransit->fresh()->current_status);
    }

    /**
     * Test DELIVERED is terminal: absolutely no further transitions or events allowed.
     */
    public function test_terminal_delivered_status_rejects_all_further_transitions_with_422(): void
    {
        $operator = User::factory()->operator()->create();
        $shipment = $this->createCorridorShipment(operator: $operator, status: ShipmentStatus::Delivered);

        // Attempting to reopen or change status to IN_TRANSIT
        $reopenPayload = [
            'status' => ShipmentStatus::InTransit->value,
            'occurred_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'description' => 'Attempting to reopen delivered consignment.',
        ];

        $response = $this->actingAs($operator)->post("/shipments/{$shipment->id}/events", $reopenPayload);
        $response->assertStatus(422);
        $this->assertEquals(ShipmentStatus::Delivered, $shipment->fresh()->current_status);

        // Attempting even same-status logging once DELIVERED
        $samePayload = [
            'status' => ShipmentStatus::Delivered->value,
            'occurred_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'description' => 'Attempting extra milestone on terminal state.',
        ];

        $responseSame = $this->actingAs($operator)->post("/shipments/{$shipment->id}/events", $samePayload);
        $responseSame->assertStatus(422);
    }

    /**
     * Test occurred_at cannot be older than the latest recorded milestone event (must be chronological).
     */
    public function test_occurred_at_older_than_latest_event_is_rejected_with_422(): void
    {
        $operator = User::factory()->operator()->create();
        $shipment = $this->createCorridorShipment(operator: $operator, status: ShipmentStatus::InTransit);

        // Seed a prior event at 2 hours ago
        TrackingEvent::factory()->create([
            'shipment_id' => $shipment->id,
            'occurred_at' => Carbon::now()->subHours(2),
            'description' => 'Departed Port of Colombo.',
        ]);

        // Attempt to log an event 5 hours ago (prior to the latest event)
        $retroactivePayload = [
            'status' => ShipmentStatus::InTransit->value,
            'occurred_at' => Carbon::now()->subHours(5)->format('Y-m-d H:i:s'),
            'description' => 'Attempting out-of-order retroactive event.',
        ];

        $response = $this->actingAs($operator)->post("/shipments/{$shipment->id}/events", $retroactivePayload);
        $response->assertStatus(422);

        // Ensure no second event was recorded
        $this->assertEquals(1, $shipment->trackingEvents()->count());
    }

    /**
     * Test occurred_at cannot be in the distant future.
     */
    public function test_occurred_at_in_future_is_rejected_with_422(): void
    {
        $operator = User::factory()->operator()->create();
        $shipment = $this->createCorridorShipment(operator: $operator, status: ShipmentStatus::InTransit);

        $futurePayload = [
            'status' => ShipmentStatus::InTransit->value,
            'occurred_at' => Carbon::now()->addDays(3)->format('Y-m-d H:i:s'),
            'description' => 'Attempting future speculative milestone.',
        ];

        $response = $this->actingAs($operator)->post("/shipments/{$shipment->id}/events", $futurePayload);
        $response->assertStatus(422);
    }

    /**
     * Test existing events are strictly append-only and never mutated.
     */
    public function test_existing_events_are_never_mutated_when_adding_new_event(): void
    {
        $operator = User::factory()->operator()->create();
        $shipment = $this->createCorridorShipment(operator: $operator, status: ShipmentStatus::Booked);

        // Create first event
        $firstEvent = TrackingEvent::factory()->create([
            'shipment_id' => $shipment->id,
            'event_type' => TrackingEventType::OrderCreated,
            'occurred_at' => Carbon::now()->subHours(3),
            'description' => 'Booking reference confirmed with Maersk Line.',
            'created_by' => $operator->id,
        ]);

        $originalAttributes = $firstEvent->fresh()->toArray();

        // Add second event through service/endpoint
        $payload = [
            'status' => ShipmentStatus::PickedUp->value,
            'event_type' => TrackingEventType::PickedUp->value,
            'occurred_at' => Carbon::now()->subHours(1)->format('Y-m-d H:i:s'),
            'description' => 'Cargo picked up from shipper warehouse.',
        ];

        $response = $this->actingAs($operator)->post("/shipments/{$shipment->id}/events", $payload);
        $response->assertRedirect(route('shipments.show', $shipment));

        // Assert 2 events exist
        $this->assertEquals(2, $shipment->trackingEvents()->count());

        // Verify first event remains identical in every field
        $refreshedFirstEvent = TrackingEvent::find($firstEvent->id)->toArray();
        $this->assertEquals($originalAttributes, $refreshedFirstEvent);
    }

    /**
     * Test customer is forbidden from adding tracking events (HTTP 403).
     */
    public function test_customer_cannot_add_tracking_events(): void
    {
        $customer = User::factory()->customer()->create();
        $shipment = $this->createCorridorShipment(customer: $customer, status: ShipmentStatus::InTransit);

        $payload = [
            'status' => ShipmentStatus::Delivered->value,
            'occurred_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'description' => 'Customer trying to mark cargo delivered.',
        ];

        $response = $this->actingAs($customer)->post("/shipments/{$shipment->id}/events", $payload);

        $response->assertStatus(403);
        $this->assertEquals(ShipmentStatus::InTransit, $shipment->fresh()->current_status);
    }

    /**
     * Test customer can view the timeline of their own shipment.
     */
    public function test_customer_can_view_own_shipment_timeline_and_location(): void
    {
        $customer = User::factory()->customer()->create();
        $shipment = $this->createCorridorShipment(customer: $customer, status: ShipmentStatus::InTransit);

        $location = Location::factory()->create(['name' => 'Suez Canal Anchorage']);

        $event = TrackingEvent::factory()->create([
            'shipment_id' => $shipment->id,
            'location_id' => $location->id,
            'event_type' => TrackingEventType::InTransit,
            'occurred_at' => Carbon::now()->subHours(4),
            'description' => 'Northbound Suez Canal transit convoy in progress.',
        ]);

        $response = $this->actingAs($customer)->get("/shipments/{$shipment->id}");

        $response->assertStatus(200);
        $response->assertSee('Suez Canal Anchorage');
        $response->assertSee('Northbound Suez Canal transit convoy in progress.');
        $response->assertSee('Milestone Timeline');
        // Customer should NOT see the "Log Tracking Milestone Event" form
        $response->assertDontSee('Log Tracking Milestone Event');
    }

    /**
     * Test customer cannot view another customer's shipment timeline.
     */
    public function test_customer_cannot_view_other_customer_shipment(): void
    {
        $owner = User::factory()->customer()->create();
        $otherCustomer = User::factory()->customer()->create();
        $shipment = $this->createCorridorShipment(customer: $owner, status: ShipmentStatus::InTransit);

        $response = $this->actingAs($otherCustomer)->get("/shipments/{$shipment->id}");

        $response->assertStatus(403);
    }
}
