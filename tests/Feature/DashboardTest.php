<?php

namespace Tests\Feature;

use App\Enums\DelayStatus;
use App\Enums\ShipmentStatus;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $operator;

    protected User $customer1;

    protected User $customer2;

    protected Carrier $carrier;

    protected Location $origin;

    protected Location $destination;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->operator = User::factory()->operator()->create();
        $this->customer1 = User::factory()->customer()->create();
        $this->customer2 = User::factory()->customer()->create();

        $this->carrier = Carrier::factory()->create(['name' => 'Pacific Maritime Express', 'code' => 'PME']);
        $this->origin = Location::factory()->create(['name' => 'Port of Singapore', 'city' => 'Singapore']);
        $this->destination = Location::factory()->create(['name' => 'Port of Rotterdam', 'city' => 'Rotterdam']);
    }

    /**
     * Test admin dashboard renders with real database aggregated metrics.
     */
    public function test_admin_dashboard_displays_real_metrics_from_database(): void
    {
        // Create 1 IN_TRANSIT, 1 DELIVERED (on-time), 1 DELAYED (with delay_hours = 18.0)
        Shipment::factory()->create([
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'current_status' => ShipmentStatus::InTransit,
            'delay_status' => DelayStatus::OnTime,
        ]);

        $deliveredShipment = Shipment::factory()->create([
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'current_status' => ShipmentStatus::Delivered,
            'delay_status' => DelayStatus::OnTime,
        ]);

        $delayedShipment = Shipment::factory()->create([
            'customer_id' => $this->customer2->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'current_status' => ShipmentStatus::InTransit,
            'delay_status' => DelayStatus::Medium,
            'delay_hours' => 18.0,
        ]);

        // Create 1 OPEN exception and 1 RESOLVED exception
        ShipmentException::factory()->create([
            'shipment_id' => $delayedShipment->id,
            'type' => 'CUSTOMS_HOLD',
            'severity' => 'CRITICAL',
            'status' => 'OPEN',
            'created_by' => $this->operator->id,
        ]);

        ShipmentException::factory()->create([
            'shipment_id' => $deliveredShipment->id,
            'type' => 'PORT_CONGESTION',
            'severity' => 'MEDIUM',
            'status' => 'RESOLVED',
            'detected_at' => now()->subHours(10),
            'resolved_at' => now()->subHours(2),
            'created_by' => $this->operator->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Administrator Console');
        $response->assertSee('Strategic Logistics KPIs');
        $response->assertSee('On-Time Delivery Rate');
        $response->assertSee('Average Delay Duration');
        $response->assertSee('Pacific Maritime Express');

        // Verify DashboardService metrics calculation directly
        $service = app(DashboardService::class);
        $metrics = $service->getAdminOperatorMetrics();

        $this->assertEquals(3, $metrics['total_shipments']);
        $this->assertEquals(2, $metrics['in_transit']);
        $this->assertEquals(1, $metrics['delivered']);
        $this->assertEquals(1, $metrics['delayed']);
        $this->assertEquals(1, $metrics['open_exceptions']);
        $this->assertEquals(100.0, $metrics['kpis']['on_time_delivery_rate']); // 1 delivered and it was ON_TIME
        $this->assertEquals(18.0, $metrics['kpis']['avg_delay_duration_hours']);
        $this->assertEquals(8.0, $metrics['kpis']['avg_exception_resolution_hours']); // 10h sub to 2h sub = 8 hours
    }

    /**
     * Test operator dashboard renders with real operational metrics.
     */
    public function test_operator_dashboard_displays_real_operational_metrics(): void
    {
        Shipment::factory()->count(2)->create([
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'current_status' => ShipmentStatus::Booked,
        ]);

        $response = $this->actingAs($this->operator)->get(route('operator.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Logistics Operations Control');
        $response->assertSee('Book New Shipment');
        $response->assertSee('Lifecycle Status Distribution');
    }

    /**
     * Test customer dashboard is strictly scoped to customer's own cargo.
     */
    public function test_customer_dashboard_is_strictly_scoped_to_owned_cargo(): void
    {
        // Customer 1 has 2 shipments
        Shipment::factory()->create([
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'shipment_number' => 'CT-CUST1-001',
            'current_status' => ShipmentStatus::InTransit,
        ]);
        Shipment::factory()->create([
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'shipment_number' => 'CT-CUST1-002',
            'current_status' => ShipmentStatus::Delivered,
        ]);

        // Customer 2 has 3 shipments (including a delayed one)
        Shipment::factory()->create([
            'customer_id' => $this->customer2->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'shipment_number' => 'CT-CUST2-SECRET-003',
            'current_status' => ShipmentStatus::InTransit,
            'delay_status' => DelayStatus::High,
        ]);

        // Customer 1 visits their dashboard
        $response = $this->actingAs($this->customer1)->get(route('customer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Customer Tracking Portal');
        $response->assertSee('CT-CUST1-001');
        $response->assertSee('CT-CUST1-002');
        $response->assertDontSee('CT-CUST2-SECRET-003');

        // Customer 1 dashboard service metrics
        $service = app(DashboardService::class);
        $cust1Metrics = $service->getCustomerMetrics($this->customer1);

        $this->assertEquals(2, $cust1Metrics['total_shipments']);
        $this->assertEquals(1, $cust1Metrics['in_transit']);
        $this->assertEquals(1, $cust1Metrics['delivered']);
        $this->assertEquals(0, $cust1Metrics['delayed']); // Customer 1 has 0 delays
    }

    /**
     * Test role-based dashboard authorization prevents cross-role access.
     */
    public function test_customer_cannot_access_admin_or_operator_dashboard(): void
    {
        $responseAdmin = $this->actingAs($this->customer1)->get(route('admin.dashboard'));
        $responseAdmin->assertStatus(403);

        $responseOperator = $this->actingAs($this->customer1)->get(route('operator.dashboard'));
        $responseOperator->assertStatus(403);
    }
}
