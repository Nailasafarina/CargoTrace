<?php

namespace Tests\Feature;

use App\Enums\DelayStatus;
use App\Enums\ShipmentStatus;
use App\Models\AuditLog;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentCrudTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that an operator can create a valid shipment with multiple manifest items,
     * and that an audit log is automatically recorded.
     */
    public function test_operator_can_create_shipment_with_multiple_items_and_audit_log_recorded(): void
    {
        $operator = User::factory()->operator()->create();
        $customer = User::factory()->customer()->create();
        $carrier = Carrier::factory()->create();
        $origin = Location::factory()->create(['name' => 'Port of Tanjung Priok']);
        $destination = Location::factory()->create(['name' => 'Port of Rotterdam']);

        $payload = [
            'customer_id' => $customer->id,
            'carrier_id' => $carrier->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
            'cargo_type' => 'Specialty Indonesian Coffee',
            'volume_cbm' => 45.5,
            'expected_departure_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'expected_arrival_at' => now()->addDays(20)->format('Y-m-d H:i:s'),
            'current_status' => ShipmentStatus::Booked->value,
            'items' => [
                [
                    'description' => 'Sumatra Mandheling Coffee',
                    'quantity' => 100,
                    'weight_kg' => 6000.00,
                ],
                [
                    'description' => 'Toraja Arabica Beans',
                    'quantity' => 50,
                    'weight_kg' => 3000.00,
                ],
            ],
        ];

        $response = $this->actingAs($operator)->post('/shipments', $payload);

        $response->assertRedirect();

        $shipment = Shipment::where('customer_id', $customer->id)->first();
        $this->assertNotNull($shipment);
        $this->assertStringStartsWith('CT-SEA-EUR-', $shipment->shipment_number);
        $this->assertEquals(9000.00, $shipment->weight_kg);
        $this->assertCount(2, $shipment->items);

        // Verify automatic audit log creation
        $auditLog = AuditLog::where('action', 'SHIPMENT_CREATED')
            ->where('entity_id', $shipment->id)
            ->first();

        $this->assertNotNull($auditLog);
        $this->assertEquals($operator->id, $auditLog->user_id);
    }

    /**
     * Test that validation fails when origin and destination are identical.
     */
    public function test_validation_fails_when_origin_and_destination_are_identical(): void
    {
        $operator = User::factory()->operator()->create();
        $location = Location::factory()->create();

        $response = $this->actingAs($operator)
            ->from('/shipments/create')
            ->post('/shipments', [
                'origin_location_id' => $location->id,
                'destination_location_id' => $location->id, // identical!
            ]);

        $response->assertRedirect('/shipments/create');
        $response->assertSessionHasErrors('destination_location_id');
    }

    /**
     * Test that validation fails when expected arrival is scheduled before departure.
     */
    public function test_validation_fails_when_expected_arrival_is_before_departure(): void
    {
        $operator = User::factory()->operator()->create();
        $origin = Location::factory()->create();
        $destination = Location::factory()->create();

        $response = $this->actingAs($operator)
            ->from('/shipments/create')
            ->post('/shipments', [
                'origin_location_id' => $origin->id,
                'destination_location_id' => $destination->id,
                'expected_departure_at' => now()->addDays(5)->format('Y-m-d H:i:s'),
                'expected_arrival_at' => now()->addDays(2)->format('Y-m-d H:i:s'), // Invalid!
            ]);

        $response->assertRedirect('/shipments/create');
        $response->assertSessionHasErrors('expected_arrival_at');
    }

    /**
     * Test that operator can update a shipment they created, and audit log records the update.
     */
    public function test_operator_can_update_own_shipment_and_audit_log_recorded(): void
    {
        $operator = User::factory()->operator()->create();
        $shipment = Shipment::factory()->create([
            'created_by' => $operator->id,
            'current_status' => ShipmentStatus::Booked,
        ]);

        $response = $this->actingAs($operator)->put("/shipments/{$shipment->id}", [
            'shipment_number' => $shipment->shipment_number,
            'customer_id' => $shipment->customer_id,
            'carrier_id' => $shipment->carrier_id,
            'origin_location_id' => $shipment->origin_location_id,
            'destination_location_id' => $shipment->destination_location_id,
            'cargo_type' => 'Updated Electronics Cargo',
            'weight_kg' => 12000.00,
            'volume_cbm' => 50.00,
            'expected_departure_at' => $shipment->expected_departure_at->format('Y-m-d H:i:s'),
            'expected_arrival_at' => $shipment->expected_arrival_at->format('Y-m-d H:i:s'),
            'current_status' => ShipmentStatus::InTransit->value,
            'delay_status' => DelayStatus::OnTime->value,
        ]);

        $response->assertRedirect();

        $shipment->refresh();
        $this->assertEquals('Updated Electronics Cargo', $shipment->cargo_type);
        $this->assertEquals(ShipmentStatus::InTransit, $shipment->current_status);

        // Verify update audit log
        $auditLog = AuditLog::where('action', 'SHIPMENT_UPDATED')
            ->where('entity_id', $shipment->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($auditLog);
        $this->assertEquals($operator->id, $auditLog->user_id);
    }

    /**
     * Test that an operator cannot update a shipment created by another operator.
     */
    public function test_operator_cannot_update_shipment_created_by_another_operator(): void
    {
        $operatorA = User::factory()->operator()->create();
        $operatorB = User::factory()->operator()->create();

        $shipment = Shipment::factory()->create([
            'created_by' => $operatorA->id,
        ]);

        $response = $this->actingAs($operatorB)->get("/shipments/{$shipment->id}/edit");
        $response->assertStatus(403);

        $response = $this->actingAs($operatorB)->put("/shipments/{$shipment->id}", [
            'shipment_number' => $shipment->shipment_number,
            'customer_id' => $shipment->customer_id,
            'carrier_id' => $shipment->carrier_id,
            'origin_location_id' => $shipment->origin_location_id,
            'destination_location_id' => $shipment->destination_location_id,
            'cargo_type' => 'Hacked Cargo',
            'weight_kg' => 1000,
            'volume_cbm' => 10,
            'expected_departure_at' => now()->format('Y-m-d H:i:s'),
            'expected_arrival_at' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'current_status' => ShipmentStatus::InTransit->value,
            'delay_status' => DelayStatus::OnTime->value,
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test that a customer can view their own shipment.
     */
    public function test_customer_can_view_own_shipment(): void
    {
        $customer = User::factory()->customer()->create();
        $shipment = Shipment::factory()->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($customer)->get("/shipments/{$shipment->id}");

        $response->assertStatus(200);
        $response->assertSee($shipment->shipment_number);
    }

    /**
     * Test that a customer cannot view another customer's shipment (403 Forbidden).
     */
    public function test_customer_cannot_view_other_customers_shipment(): void
    {
        $customerA = User::factory()->customer()->create();
        $customerB = User::factory()->customer()->create();

        $shipmentB = Shipment::factory()->create(['customer_id' => $customerB->id]);

        $response = $this->actingAs($customerA)->get("/shipments/{$shipmentB->id}");

        $response->assertStatus(403);
    }

    /**
     * Test that a customer only sees their own shipments in the index listing.
     */
    public function test_customer_sees_only_own_shipments_in_index(): void
    {
        $customerA = User::factory()->customer()->create();
        $customerB = User::factory()->customer()->create();

        $shipmentA = Shipment::factory()->create([
            'customer_id' => $customerA->id,
            'shipment_number' => 'CT-CUSTOMER-A-001',
        ]);

        $shipmentB = Shipment::factory()->create([
            'customer_id' => $customerB->id,
            'shipment_number' => 'CT-CUSTOMER-B-999',
        ]);

        $response = $this->actingAs($customerA)->get('/shipments');

        $response->assertStatus(200);
        $response->assertSee('CT-CUSTOMER-A-001');
        $response->assertDontSee('CT-CUSTOMER-B-999');
    }

    /**
     * Test that an administrator can delete a shipment, and an audit log is recorded.
     */
    public function test_admin_can_delete_shipment_and_audit_log_recorded(): void
    {
        $admin = User::factory()->admin()->create();
        $shipment = Shipment::factory()->create();

        $response = $this->actingAs($admin)->delete("/shipments/{$shipment->id}");

        $response->assertRedirect('/shipments');
        $this->assertDatabaseMissing('shipments', ['id' => $shipment->id]);

        $auditLog = AuditLog::where('action', 'SHIPMENT_DELETED')
            ->where('entity_id', $shipment->id)
            ->first();

        $this->assertNotNull($auditLog);
        $this->assertEquals($admin->id, $auditLog->user_id);
    }

    /**
     * Test that operators and customers are forbidden from deleting shipments (403 Forbidden).
     */
    public function test_operator_and_customer_cannot_delete_shipment(): void
    {
        $operator = User::factory()->operator()->create();
        $customer = User::factory()->customer()->create();
        $shipment = Shipment::factory()->create([
            'customer_id' => $customer->id,
            'created_by' => $operator->id,
        ]);

        // Operator attempt
        $response = $this->actingAs($operator)->delete("/shipments/{$shipment->id}");
        $response->assertStatus(403);

        // Customer attempt
        $response = $this->actingAs($customer)->delete("/shipments/{$shipment->id}");
        $response->assertStatus(403);

        $this->assertDatabaseHas('shipments', ['id' => $shipment->id]);
    }
}
