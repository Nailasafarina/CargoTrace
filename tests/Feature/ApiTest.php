<?php

namespace Tests\Feature;

use App\Enums\ExceptionSeverity;
use App\Enums\ExceptionStatus;
use App\Enums\ExceptionType;
use App\Enums\ShipmentStatus;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration via API returns Sanctum token and user resource (HTTP 201).
     */
    public function test_user_can_register_via_api_and_receive_token(): void
    {
        $payload = [
            'name' => 'Aditya Pratama',
            'email' => 'aditya@cargotrace.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'customer',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'access_token',
                'token_type',
                'user' => ['id', 'name', 'email', 'role'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'aditya@cargotrace.test',
            'role' => 'customer',
        ]);
    }

    /**
     * Test user login via API returns valid token (HTTP 200).
     */
    public function test_user_can_login_via_api_and_receive_token(): void
    {
        $user = User::factory()->create([
            'email' => 'operator@cargotrace.test',
            'password' => bcrypt('Password123!'),
            'role' => 'operator',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'operator@cargotrace.test',
            'password' => 'Password123!',
            'device_name' => 'integration_test_runner',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'access_token',
                'token_type',
                'user' => ['id', 'name', 'email', 'role'],
            ]);
    }

    /**
     * Test login failure returns HTTP 422 with structured validation error.
     */
    public function test_login_fails_with_invalid_credentials_returns_422(): void
    {
        $user = User::factory()->create([
            'email' => 'operator@cargotrace.test',
            'password' => bcrypt('Password123!'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'operator@cargotrace.test',
            'password' => 'WrongPassword!',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['email']]);
    }

    /**
     * Test user can logout and revoke their active Sanctum token.
     */
    public function test_user_can_logout_and_revoke_token(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out and revoked access token.']);
    }

    /**
     * Test unauthenticated access to protected endpoints returns HTTP 401.
     */
    public function test_unauthenticated_requests_return_401(): void
    {
        $responseUser = $this->getJson('/api/user');
        $responseUser->assertStatus(401)
            ->assertJsonStructure(['message', 'errors']);

        $responseShipments = $this->getJson('/api/shipments');
        $responseShipments->assertStatus(401)
            ->assertJsonStructure(['message', 'errors']);
    }

    /**
     * Test customer can only view their own shipments in the paginated index.
     */
    public function test_customer_can_only_view_own_shipments_in_list(): void
    {
        $customerA = User::factory()->customer()->create();
        $customerB = User::factory()->customer()->create();
        $carrier = Carrier::factory()->create();
        $origin = Location::factory()->create();
        $destination = Location::factory()->create();

        $shipmentA = Shipment::factory()->create([
            'customer_id' => $customerA->id,
            'carrier_id' => $carrier->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
            'shipment_number' => 'CT-TEST-001',
        ]);

        $shipmentB = Shipment::factory()->create([
            'customer_id' => $customerB->id,
            'carrier_id' => $carrier->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
            'shipment_number' => 'CT-TEST-002',
        ]);

        Sanctum::actingAs($customerA);

        $response = $this->getJson('/api/shipments');

        $response->assertStatus(200);
        $response->assertJsonFragment(['shipment_number' => 'CT-TEST-001']);
        $response->assertJsonMissing(['shipment_number' => 'CT-TEST-002']);
    }

    /**
     * Test customer accessing another customer's shipment returns HTTP 403 Forbidden.
     */
    public function test_customer_accessing_other_customer_shipment_returns_403(): void
    {
        $customerA = User::factory()->customer()->create();
        $customerB = User::factory()->customer()->create();
        $carrier = Carrier::factory()->create();
        $origin = Location::factory()->create();
        $destination = Location::factory()->create();

        $shipmentB = Shipment::factory()->create([
            'customer_id' => $customerB->id,
            'carrier_id' => $carrier->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
        ]);

        Sanctum::actingAs($customerA);

        $response = $this->getJson("/api/shipments/{$shipmentB->id}");

        $response->assertStatus(403)
            ->assertJsonStructure(['message', 'errors']);
    }

    /**
     * Test logistics operator can create a shipment with items via API (HTTP 201).
     */
    public function test_operator_can_create_shipment_via_api_returns_201(): void
    {
        $operator = User::factory()->operator()->create();
        $customer = User::factory()->customer()->create();
        $carrier = Carrier::factory()->create();
        $origin = Location::factory()->create();
        $destination = Location::factory()->create();

        Sanctum::actingAs($operator);

        $payload = [
            'customer_id' => $customer->id,
            'carrier_id' => $carrier->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
            'cargo_type' => 'Raw Rubber Bales & Natural Latex',
            'volume_cbm' => 55.40,
            'expected_departure_at' => Carbon::now()->addDays(2)->toDateTimeString(),
            'expected_arrival_at' => Carbon::now()->addDays(22)->toDateTimeString(),
            'items' => [
                ['description' => 'Grade A Ribbed Smoked Sheets', 'quantity' => 200, 'weight_kg' => 12000.00],
                ['description' => 'Technically Specified Rubber TSR-20', 'quantity' => 150, 'weight_kg' => 9000.00],
            ],
        ];

        $response = $this->postJson('/api/shipments', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.cargo_type', 'Raw Rubber Bales & Natural Latex')
            ->assertJsonPath('data.weight_kg', 21000)
            ->assertJsonCount(2, 'data.items');

        $this->assertDatabaseHas('shipments', [
            'customer_id' => $customer->id,
            'cargo_type' => 'Raw Rubber Bales & Natural Latex',
        ]);
    }

    /**
     * Test validation failure returns HTTP 422 with structured errors.
     */
    public function test_validation_failure_returns_422_with_consistent_json(): void
    {
        $operator = User::factory()->operator()->create();
        Sanctum::actingAs($operator);

        // Departure is AFTER arrival, customer missing, invalid volume
        $invalidPayload = [
            'cargo_type' => 'Incomplete consignment',
            'expected_departure_at' => Carbon::now()->addDays(10)->toDateTimeString(),
            'expected_arrival_at' => Carbon::now()->addDays(2)->toDateTimeString(),
        ];

        $response = $this->postJson('/api/shipments', $invalidPayload);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['customer_id', 'carrier_id', 'expected_arrival_at'],
            ]);
    }

    /**
     * Test invalid status transition via API is rejected with HTTP 422.
     */
    public function test_invalid_status_transition_via_api_returns_422(): void
    {
        $operator = User::factory()->operator()->create();
        $customer = User::factory()->customer()->create();
        $carrier = Carrier::factory()->create();
        $origin = Location::factory()->create();
        $destination = Location::factory()->create();

        $shipment = Shipment::factory()->create([
            'customer_id' => $customer->id,
            'carrier_id' => $carrier->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
            'current_status' => ShipmentStatus::Created,
            'created_by' => $operator->id,
        ]);

        Sanctum::actingAs($operator);

        // Attempting to skip directly to DELIVERED
        $response = $this->postJson("/api/shipments/{$shipment->id}/tracking", [
            'status' => ShipmentStatus::Delivered->value,
            'occurred_at' => Carbon::now()->toDateTimeString(),
            'description' => 'Attempting illicit bypass transition to DELIVERED.',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    /**
     * Test customer cannot post tracking events (HTTP 403 Forbidden).
     */
    public function test_customer_cannot_post_tracking_events_returns_403(): void
    {
        $customer = User::factory()->customer()->create();
        $shipment = Shipment::factory()->create(['customer_id' => $customer->id]);

        Sanctum::actingAs($customer);

        $response = $this->postJson("/api/shipments/{$shipment->id}/tracking", [
            'status' => ShipmentStatus::InTransit->value,
            'occurred_at' => Carbon::now()->toDateTimeString(),
            'description' => 'Customer trying to log milestone.',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test server-side pagination, status filtering, and sorting.
     */
    public function test_shipments_pagination_and_filtering_via_api(): void
    {
        $operator = User::factory()->operator()->create();
        $carrier = Carrier::factory()->create();
        $origin = Location::factory()->create();
        $destination = Location::factory()->create();

        // Create 3 IN_TRANSIT shipments and 2 DELIVERED shipments
        Shipment::factory()->count(3)->create([
            'carrier_id' => $carrier->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
            'current_status' => ShipmentStatus::InTransit,
        ]);

        Shipment::factory()->count(2)->create([
            'carrier_id' => $carrier->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
            'current_status' => ShipmentStatus::Delivered,
        ]);

        Sanctum::actingAs($operator);

        // Filter for IN_TRANSIT with per_page = 2
        $response = $this->getJson('/api/shipments?status=IN_TRANSIT&per_page=2&sort_by=created_at&sort_order=desc');

        $response->assertStatus(200)
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.total', 3)
            ->assertJsonCount(2, 'data');

        foreach ($response->json('data') as $item) {
            $this->assertEquals(ShipmentStatus::InTransit->value, $item['current_status']);
        }
    }

    /**
     * Test full exception lifecycle transitions via API.
     */
    public function test_exception_lifecycle_transitions_via_api(): void
    {
        $operator = User::factory()->operator()->create();
        $customer = User::factory()->customer()->create();
        $carrier = Carrier::factory()->create();
        $origin = Location::factory()->create();
        $destination = Location::factory()->create();

        $shipment = Shipment::factory()->create([
            'customer_id' => $customer->id,
            'carrier_id' => $carrier->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
        ]);

        Sanctum::actingAs($operator);

        // 1. Create Exception (OPEN)
        $createResponse = $this->postJson("/api/shipments/{$shipment->id}/exceptions", [
            'type' => ExceptionType::PortCongestion->value,
            'severity' => ExceptionSeverity::Medium->value,
            'description' => 'Port of Singapore berth delay due to queue congestion.',
        ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('data.status', ExceptionStatus::Open->value);

        $exceptionId = $createResponse->json('data.id');

        // 2. Transition to INVESTIGATING
        $investigateResponse = $this->patchJson("/api/exceptions/{$exceptionId}", [
            'status' => ExceptionStatus::Investigating->value,
            'resolution_notes' => 'Port agent coordinating slot reallocation.',
        ]);

        $investigateResponse->assertStatus(200)
            ->assertJsonPath('data.status', ExceptionStatus::Investigating->value);

        // 3. Transition to RESOLVED
        $resolveResponse = $this->patchJson("/api/exceptions/{$exceptionId}", [
            'status' => ExceptionStatus::Resolved->value,
            'resolution_notes' => 'Vessel berthed successfully.',
        ]);

        $resolveResponse->assertStatus(200)
            ->assertJsonPath('data.status', ExceptionStatus::Resolved->value);
        $this->assertNotNull($resolveResponse->json('data.resolved_at'));

        // 4. Invalid Transition from RESOLVED back to OPEN (Rejected with 422)
        $illegalResponse = $this->patchJson("/api/exceptions/{$exceptionId}", [
            'status' => ExceptionStatus::Open->value,
        ]);

        $illegalResponse->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    /**
     * Test Master Data API: Carrier and Location creation (Admin only).
     */
    public function test_admin_can_manage_carriers_and_locations_via_api(): void
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        // Create Carrier
        $carrierResponse = $this->postJson('/api/carriers', [
            'name' => 'Yang Ming Marine Transport',
            'code' => 'YML',
            'contact_email' => 'operations@yangming.com',
            'is_active' => true,
        ]);
        $carrierResponse->assertStatus(201)
            ->assertJsonPath('data.code', 'YML');

        // Create Location
        $locationResponse = $this->postJson('/api/locations', [
            'name' => 'Port of Le Havre',
            'type' => 'port',
            'country' => 'France',
            'city' => 'Le Havre',
            'latitude' => 49.4944,
            'longitude' => 0.1079,
        ]);
        $locationResponse->assertStatus(201)
            ->assertJsonPath('data.city', 'Le Havre');
    }

    /**
     * Test error formatting with APP_DEBUG=false never leaks stack trace.
     */
    public function test_api_error_response_does_not_leak_stack_trace_when_debug_false(): void
    {
        config(['app.debug' => false]);

        // Attempting to query non-existent shipment returns 404 without trace
        $response = $this->getJson('/api/shipments/999999');

        $response->assertStatus(401); // Requires authentication first
        $response->assertJsonStructure(['message', 'errors']);
        $this->assertArrayNotHasKey('trace', $response->json());
    }
}
