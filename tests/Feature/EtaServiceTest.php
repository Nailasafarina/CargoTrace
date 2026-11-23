<?php

namespace Tests\Feature;

use App\Enums\DelayStatus;
use App\Enums\ShipmentStatus;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\Shipment;
use App\Models\User;
use App\Services\EtaService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EtaServiceTest extends TestCase
{
    use RefreshDatabase;

    protected EtaService $etaService;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('id');
        $this->etaService = app(EtaService::class);
    }

    /**
     * Helper to create a test shipment.
     */
    protected function createShipment(
        Carbon $expectedArrival,
        ?Carbon $estimatedArrival = null,
        DelayStatus $delayStatus = DelayStatus::OnTime
    ): Shipment {
        $operator = User::factory()->operator()->create();
        $customer = User::factory()->customer()->create();
        $carrier = Carrier::factory()->create();
        $origin = Location::factory()->create();
        $destination = Location::factory()->create();

        return Shipment::factory()->create([
            'customer_id' => $customer->id,
            'carrier_id' => $carrier->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
            'expected_departure_at' => $expectedArrival->copy()->subDays(10),
            'expected_arrival_at' => $expectedArrival,
            'estimated_arrival_at' => $estimatedArrival ?? $expectedArrival,
            'current_status' => ShipmentStatus::InTransit,
            'delay_status' => $delayStatus,
            'created_by' => $operator->id,
        ]);
    }

    /**
     * Test boundary rule: Delay exactly at 6 hours boundary is classified as LOW.
     */
    public function test_exact_boundary_delay_rule_low_severity_at_6_hours(): void
    {
        $expected = Carbon::parse('2026-03-10 10:00:00');
        $estimated = Carbon::parse('2026-03-10 16:00:00'); // Exactly 6 hours delay
        $shipment = $this->createShipment($expected, $estimated);

        // Threshold set to 6 hours
        $result = $this->etaService->evaluate($shipment, $estimated, $expected, 6);

        $this->assertTrue($result->isDelayed);
        $this->assertEquals(6, $result->delayHours);
        $this->assertEquals('LOW', $result->classification);
        $this->assertEquals(DelayStatus::Low, $result->delayStatus);
        $this->assertStringContainsString('sebesar 6 jam; threshold 6 jam → LOW', $result->reason);
    }

    /**
     * Test boundary rule: Delay exactly at 24 hours boundary is classified as MEDIUM.
     */
    public function test_exact_boundary_delay_rule_medium_severity_at_24_hours(): void
    {
        $expected = Carbon::parse('2026-03-10 10:00:00');
        $estimated = Carbon::parse('2026-03-11 10:00:00'); // Exactly 24 hours delay
        $shipment = $this->createShipment($expected, $estimated);

        // Threshold set to 12 hours
        $result = $this->etaService->evaluate($shipment, $estimated, $expected, 12);

        $this->assertTrue($result->isDelayed);
        $this->assertEquals(24, $result->delayHours);
        $this->assertEquals('MEDIUM', $result->classification);
        $this->assertEquals(DelayStatus::Medium, $result->delayStatus);
        $this->assertStringContainsString('sebesar 24 jam; threshold 12 jam → MEDIUM', $result->reason);
    }

    /**
     * Test boundary rule: Delay exceeding 24 hours is classified as HIGH.
     */
    public function test_high_severity_delay_exceeding_24_hours(): void
    {
        $expected = Carbon::parse('2026-03-10 10:00:00');
        $estimated = Carbon::parse('2026-03-11 11:00:00'); // 25 hours delay (> 24 hours)
        $shipment = $this->createShipment($expected, $estimated);

        // Group default threshold: 24 hours
        $result = $this->etaService->evaluate($shipment, $estimated, $expected, 24);

        $this->assertTrue($result->isDelayed);
        $this->assertEquals(25, $result->delayHours);
        $this->assertEquals('HIGH', $result->classification);
        $this->assertEquals(DelayStatus::High, $result->delayStatus);
        $this->assertStringContainsString('sebesar 25 jam; threshold 24 jam → HIGH', $result->reason);
    }

    /**
     * Test delay below configured threshold is marked ON_TIME (tolerance buffer).
     */
    public function test_delay_below_threshold_is_marked_on_time(): void
    {
        $expected = Carbon::parse('2026-03-10 10:00:00');
        $estimated = Carbon::parse('2026-03-10 18:00:00'); // 8 hours delay
        $shipment = $this->createShipment($expected, $estimated);

        // Threshold set to 24 hours (8h delay is below 24h threshold)
        $result = $this->etaService->evaluate($shipment, $estimated, $expected, 24);

        $this->assertFalse($result->isDelayed);
        $this->assertEquals(8, $result->delayHours);
        $this->assertEquals(DelayStatus::OnTime, $result->delayStatus);
        $this->assertStringContainsString('di bawah threshold 24 jam → ON_TIME', $result->reason);
    }

    /**
     * Test changing threshold changes the evaluation outcome dynamically.
     */
    public function test_changing_threshold_changes_delay_evaluation_outcome(): void
    {
        $expected = Carbon::parse('2026-03-13 18:00:00');
        $estimated = Carbon::parse('2026-03-14 10:00:00'); // 16 hours delay
        $shipment = $this->createShipment($expected, $estimated);

        // Scenario A: Threshold set to 12 hours (16h >= 12h -> DELAYED: MEDIUM)
        Setting::set('delay_threshold_hours', 12, 'integer');
        $resultA = $this->etaService->evaluate($shipment);

        $this->assertTrue($resultA->isDelayed);
        $this->assertEquals(DelayStatus::Medium, $resultA->delayStatus);
        $this->assertEquals('MEDIUM', $resultA->classification);
        $this->assertStringContainsString('threshold 12 jam → MEDIUM', $resultA->reason);

        // Scenario B: Threshold adjusted by Admin to 24 hours (16h < 24h -> ON_TIME)
        Setting::set('delay_threshold_hours', 24, 'integer');
        $resultB = $this->etaService->evaluate($shipment);

        $this->assertFalse($resultB->isDelayed);
        $this->assertEquals(DelayStatus::OnTime, $resultB->delayStatus);
        $this->assertStringContainsString('di bawah threshold 24 jam → ON_TIME', $resultB->reason);
    }

    /**
     * Test evaluateAndSave persists data, triggers notifications and audit logs when shipment becomes delayed.
     */
    public function test_evaluate_and_save_triggers_notifications_and_audit_log_when_delayed(): void
    {
        $operator = User::factory()->operator()->create();
        $expected = Carbon::now()->addDays(5);
        $estimated = Carbon::now()->addDays(5)->addHours(30); // 30 hours delay
        $shipment = $this->createShipment($expected, $estimated, DelayStatus::OnTime);

        Setting::set('delay_threshold_hours', 24, 'integer');

        $result = $this->etaService->evaluateAndSave($shipment);

        $this->assertTrue($result->isDelayed);
        $this->assertEquals(DelayStatus::High, $shipment->fresh()->delay_status);
        $this->assertEquals(30, $shipment->fresh()->delay_hours);
        $this->assertNotNull($shipment->fresh()->delay_reason);

        // Verify Customer In-App Notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $shipment->customer_id,
            'shipment_id' => $shipment->id,
            'type' => 'SHIPMENT_DELAY_ALERT',
        ]);

        // Verify Operator In-App Notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $operator->id,
            'shipment_id' => $shipment->id,
            'type' => 'SHIPMENT_DELAY_INTERNAL',
        ]);

        // Verify Audit Log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'SHIPMENT_DELAY_DETECTED',
            'entity_type' => Shipment::class,
            'entity_id' => $shipment->id,
        ]);
    }

    /**
     * Test scheduled artisan command shipments:evaluate-delays.
     */
    public function test_artisan_command_evaluate_delays_executes_cleanly(): void
    {
        $expected = Carbon::now()->addDays(5);
        $estimated = Carbon::now()->addDays(5)->addHours(14);
        $this->createShipment($expected, $estimated);

        $this->artisan('shipments:evaluate-delays', ['--threshold' => 12])
            ->assertExitCode(0);
    }

    /**
     * Test Admin can update delay threshold via web route.
     */
    public function test_admin_can_update_threshold_setting_via_http(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->put('/admin/settings', [
            'delay_threshold_hours' => 18,
            're_evaluate_now' => 1,
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $this->assertEquals(18, Setting::get('delay_threshold_hours'));
    }
}
