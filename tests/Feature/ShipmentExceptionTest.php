<?php

namespace Tests\Feature;

use App\Enums\ExceptionSeverity;
use App\Enums\ExceptionStatus;
use App\Enums\ExceptionType;
use App\Enums\ShipmentStatus;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentExceptionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper to create a test shipment.
     */
    protected function createShipment(?User $operator = null, ?User $customer = null): Shipment
    {
        $operator = $operator ?? User::factory()->operator()->create();
        $customer = $customer ?? User::factory()->customer()->create();
        $carrier = Carrier::factory()->create();
        $origin = Location::factory()->create();
        $destination = Location::factory()->create();

        return Shipment::factory()->create([
            'customer_id' => $customer->id,
            'carrier_id' => $carrier->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
            'current_status' => ShipmentStatus::InTransit,
            'created_by' => $operator->id,
        ]);
    }

    /**
     * Test operator can create an exception with automatic detected_at, notifications, and audit trail.
     */
    public function test_operator_can_create_exception_with_automatic_detected_at(): void
    {
        $operator = User::factory()->operator()->create();
        $customer = User::factory()->customer()->create();
        $shipment = $this->createShipment($operator, $customer);

        $payload = [
            'type' => ExceptionType::CustomsHold->value,
            'severity' => ExceptionSeverity::High->value,
            'description' => 'Container placed on hold at Rotterdam Customs for phytosanitary inspection.',
        ];

        $response = $this->actingAs($operator)->post("/shipments/{$shipment->id}/exceptions", $payload);

        $response->assertRedirect(route('shipments.show', $shipment));
        $response->assertSessionHas('status');

        $exception = ShipmentException::where('shipment_id', $shipment->id)->first();
        $this->assertNotNull($exception);
        $this->assertEquals(ExceptionType::CustomsHold, $exception->type);
        $this->assertEquals(ExceptionStatus::Open, $exception->status);
        $this->assertNotNull($exception->detected_at);
        $this->assertNull($exception->resolved_at);

        // Verify Customer Notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $customer->id,
            'shipment_id' => $shipment->id,
            'type' => 'SHIPMENT_EXCEPTION_ALERT',
        ]);

        // Verify Operator Notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $operator->id,
            'shipment_id' => $shipment->id,
            'type' => 'SHIPMENT_EXCEPTION_INTERNAL',
        ]);

        // Verify Audit Log
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $operator->id,
            'action' => 'EXCEPTION_CREATED',
            'entity_type' => ShipmentException::class,
            'entity_id' => $exception->id,
        ]);
    }

    /**
     * Test valid transition from OPEN to INVESTIGATING.
     */
    public function test_valid_transition_from_open_to_investigating(): void
    {
        $operator = User::factory()->operator()->create();
        $shipment = $this->createShipment($operator);
        $exception = ShipmentException::factory()->create([
            'shipment_id' => $shipment->id,
            'status' => ExceptionStatus::Open,
            'created_by' => $operator->id,
        ]);

        $response = $this->actingAs($operator)->patch("/exceptions/{$exception->id}/status", [
            'status' => ExceptionStatus::Investigating->value,
            'resolution_notes' => 'Customs broker assigned to file revised commercial invoice.',
        ]);

        $response->assertRedirect(route('shipments.show', $shipment));
        $this->assertEquals(ExceptionStatus::Investigating, $exception->fresh()->status);
        $this->assertNull($exception->fresh()->resolved_at);
    }

    /**
     * Test valid transition to RESOLVED automatically populates resolved_at timestamp.
     */
    public function test_valid_transition_to_resolved_populates_resolved_at(): void
    {
        $operator = User::factory()->operator()->create();
        $customer = User::factory()->customer()->create();
        $shipment = $this->createShipment($operator, $customer);
        $exception = ShipmentException::factory()->create([
            'shipment_id' => $shipment->id,
            'status' => ExceptionStatus::Investigating,
            'resolved_at' => null,
            'created_by' => $operator->id,
        ]);

        $response = $this->actingAs($operator)->patch("/exceptions/{$exception->id}/status", [
            'status' => ExceptionStatus::Resolved->value,
            'resolution_notes' => 'Customs release order confirmed. Clearance seal verified.',
        ]);

        $response->assertRedirect(route('shipments.show', $shipment));
        $refreshed = $exception->fresh();
        $this->assertEquals(ExceptionStatus::Resolved, $refreshed->status);
        $this->assertNotNull($refreshed->resolved_at);

        // Verify Customer Resolution Notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $customer->id,
            'shipment_id' => $shipment->id,
            'type' => 'SHIPMENT_EXCEPTION_RESOLVED',
        ]);

        // Verify Audit Log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'EXCEPTION_STATUS_UPDATED',
            'entity_type' => ShipmentException::class,
            'entity_id' => $exception->id,
        ]);
    }

    /**
     * Test invalid transition from terminal RESOLVED state is rejected with HTTP 422.
     */
    public function test_invalid_exception_status_transition_is_rejected_with_422(): void
    {
        $operator = User::factory()->operator()->create();
        $shipment = $this->createShipment($operator);
        $exception = ShipmentException::factory()->create([
            'shipment_id' => $shipment->id,
            'status' => ExceptionStatus::Resolved,
            'resolved_at' => Carbon::now()->subHours(2),
            'created_by' => $operator->id,
        ]);

        // Attempting to reopen a resolved exception
        $response = $this->actingAs($operator)->patch("/exceptions/{$exception->id}/status", [
            'status' => ExceptionStatus::Open->value,
        ]);

        $response->assertStatus(422);
        $this->assertEquals(ExceptionStatus::Resolved, $exception->fresh()->status);
    }

    /**
     * Test customer cannot create or update exceptions (HTTP 403).
     */
    public function test_customer_cannot_create_or_update_exceptions(): void
    {
        $customer = User::factory()->customer()->create();
        $shipment = $this->createShipment(customer: $customer);

        // 1. Attempt create exception
        $createResponse = $this->actingAs($customer)->post("/shipments/{$shipment->id}/exceptions", [
            'type' => ExceptionType::Damaged->value,
            'description' => 'Customer trying to file exception directly.',
        ]);
        $createResponse->assertStatus(403);

        // 2. Attempt update status of existing exception
        $exception = ShipmentException::factory()->create([
            'shipment_id' => $shipment->id,
            'status' => ExceptionStatus::Open,
        ]);

        $updateResponse = $this->actingAs($customer)->patch("/exceptions/{$exception->id}/status", [
            'status' => ExceptionStatus::Resolved->value,
        ]);
        $updateResponse->assertStatus(403);
    }
}
