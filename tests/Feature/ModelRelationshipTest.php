<?php

namespace Tests\Feature;

use App\Enums\ExceptionSeverity;
use App\Enums\ExceptionStatus;
use App\Enums\ExceptionType;
use App\Enums\TrackingEventType;
use App\Models\AuditLog;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\ShipmentItem;
use App\Models\TrackingEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Carrier to Shipments relationship (1:N).
     */
    public function test_carrier_has_many_shipments(): void
    {
        $carrier = Carrier::factory()->create();
        $shipment = Shipment::factory()->create(['carrier_id' => $carrier->id]);

        $this->assertTrue($carrier->shipments->contains($shipment));
        $this->assertInstanceOf(Carrier::class, $shipment->carrier);
        $this->assertEquals($carrier->id, $shipment->carrier->id);
    }

    /**
     * Test Location origin and destination relationships (1:N).
     */
    public function test_location_has_origin_and_destination_shipments(): void
    {
        $origin = Location::factory()->create(['name' => 'Port of Jakarta']);
        $destination = Location::factory()->create(['name' => 'Port of Rotterdam']);

        $shipment = Shipment::factory()->create([
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
        ]);

        $this->assertTrue($origin->originShipments->contains($shipment));
        $this->assertTrue($destination->destinationShipments->contains($shipment));
        $this->assertEquals('Port of Jakarta', $shipment->originLocation->name);
        $this->assertEquals('Port of Rotterdam', $shipment->destinationLocation->name);
    }

    /**
     * Test Shipment relations to User, Items, TrackingEvents, and Exceptions.
     */
    public function test_shipment_belongs_to_customer_and_has_children(): void
    {
        $customer = User::factory()->customer()->create();
        $operator = User::factory()->operator()->create();

        $shipment = Shipment::factory()->create([
            'customer_id' => $customer->id,
            'created_by' => $operator->id,
        ]);

        $item = ShipmentItem::factory()->create([
            'shipment_id' => $shipment->id,
            'description' => 'Industrial Sensors',
            'quantity' => 100,
        ]);

        $event = TrackingEvent::factory()->create([
            'shipment_id' => $shipment->id,
            'event_type' => TrackingEventType::InTransit,
            'created_by' => $operator->id,
        ]);

        $exception = ShipmentException::factory()->create([
            'shipment_id' => $shipment->id,
            'type' => ExceptionType::CustomsHold,
            'severity' => ExceptionSeverity::High,
            'status' => ExceptionStatus::Open,
            'created_by' => $operator->id,
        ]);

        $notification = Notification::factory()->create([
            'user_id' => $customer->id,
            'shipment_id' => $shipment->id,
        ]);

        // Customer perspective
        $this->assertTrue($customer->shipments->contains($shipment));
        $this->assertEquals($customer->id, $shipment->customer->id);
        $this->assertEquals($operator->id, $shipment->creator->id);

        // Children relations
        $this->assertTrue($shipment->items->contains($item));
        $this->assertTrue($shipment->trackingEvents->contains($event));
        $this->assertTrue($shipment->exceptions->contains($exception));
        $this->assertTrue($shipment->notifications->contains($notification));

        // Child back to parent
        $this->assertEquals($shipment->id, $item->shipment->id);
        $this->assertEquals($shipment->id, $event->shipment->id);
        $this->assertEquals($shipment->id, $exception->shipment->id);
        $this->assertEquals($shipment->id, $notification->shipment->id);
    }

    /**
     * Test TrackingEvent relations to Location and User creator.
     */
    public function test_tracking_event_belongs_to_location_and_creator(): void
    {
        $operator = User::factory()->operator()->create();
        $location = Location::factory()->create();
        $event = TrackingEvent::factory()->create([
            'location_id' => $location->id,
            'created_by' => $operator->id,
        ]);

        $this->assertEquals($location->id, $event->location->id);
        $this->assertEquals($operator->id, $event->creator->id);
        $this->assertTrue($location->trackingEvents->contains($event));
    }

    /**
     * Test AuditLog relations to User.
     */
    public function test_audit_log_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $log = AuditLog::create([
            'user_id' => $user->id,
            'action' => 'SHIPMENT_UPDATED',
            'entity_type' => Shipment::class,
            'entity_id' => 1,
            'old_values' => ['status' => 'BOOKED'],
            'new_values' => ['status' => 'IN_TRANSIT'],
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertEquals($user->id, $log->user->id);
        $this->assertTrue($user->auditLogs->contains($log));
    }

    /**
     * Test Cascade Deletion: deleting a shipment cleans up items, events, and exceptions
     * but does NOT delete referenced carriers or locations.
     */
    public function test_deleting_shipment_cascades_to_children_safely(): void
    {
        $carrier = Carrier::factory()->create();
        $location = Location::factory()->create();

        $shipment = Shipment::factory()->create([
            'carrier_id' => $carrier->id,
            'origin_location_id' => $location->id,
            'destination_location_id' => $location->id,
        ]);

        $item = ShipmentItem::factory()->create(['shipment_id' => $shipment->id]);
        $event = TrackingEvent::factory()->create(['shipment_id' => $shipment->id]);
        $exception = ShipmentException::factory()->create(['shipment_id' => $shipment->id]);

        $this->assertDatabaseHas('shipments', ['id' => $shipment->id]);
        $this->assertDatabaseHas('shipment_items', ['id' => $item->id]);
        $this->assertDatabaseHas('tracking_events', ['id' => $event->id]);
        $this->assertDatabaseHas('shipment_exceptions', ['id' => $exception->id]);

        // Delete the shipment
        $shipment->delete();

        // Children must be cascaded
        $this->assertDatabaseMissing('shipments', ['id' => $shipment->id]);
        $this->assertDatabaseMissing('shipment_items', ['id' => $item->id]);
        $this->assertDatabaseMissing('tracking_events', ['id' => $event->id]);
        $this->assertDatabaseMissing('shipment_exceptions', ['id' => $exception->id]);

        // Master data must remain untouched
        $this->assertDatabaseHas('carriers', ['id' => $carrier->id]);
        $this->assertDatabaseHas('locations', ['id' => $location->id]);
    }
}
