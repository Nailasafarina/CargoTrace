<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $operator;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create(['name' => 'Admin Alex', 'email' => 'alex@cargotrace.test']);
        $this->operator = User::factory()->operator()->create(['name' => 'Operator Bob', 'email' => 'bob@cargotrace.test']);
        $this->customer = User::factory()->customer()->create(['name' => 'Customer Charlie', 'email' => 'charlie@cargotrace.test']);
    }

    /**
     * Test platform administrator can access audit log page.
     */
    public function test_admin_can_view_audit_logs(): void
    {
        AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'setting_updated',
            'entity_type' => 'Setting',
            'entity_id' => 1,
            'old_values' => ['delay_threshold_hours' => 24],
            'new_values' => ['delay_threshold_hours' => 18],
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.audit-logs.index'));

        $response->assertStatus(200);
        $response->assertSee('System Audit Trails &amp; Compliance', false);
        $response->assertSee('setting_updated');
        $response->assertSee('Admin Alex');
    }

    /**
     * Test logistics operator cannot access audit logs (403 Forbidden).
     */
    public function test_operator_cannot_view_audit_logs(): void
    {
        $response = $this->actingAs($this->operator)->get(route('admin.audit-logs.index'));
        $response->assertStatus(403);
    }

    /**
     * Test customer cannot access audit logs (403 Forbidden).
     */
    public function test_customer_cannot_view_audit_logs(): void
    {
        $response = $this->actingAs($this->customer)->get(route('admin.audit-logs.index'));
        $response->assertStatus(403);
    }

    /**
     * Test guest is redirected to login.
     */
    public function test_guest_is_redirected_from_audit_logs(): void
    {
        $response = $this->get(route('admin.audit-logs.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test filtering audit logs by actor (user_id).
     */
    public function test_admin_can_filter_audit_logs_by_actor(): void
    {
        AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'created',
            'entity_type' => 'Carrier',
            'entity_id' => 10,
            'new_values' => ['name' => 'Admin Logged Carrier'],
        ]);

        AuditLog::create([
            'user_id' => $this->operator->id,
            'action' => 'created',
            'entity_type' => 'Shipment',
            'entity_id' => 20,
            'new_values' => ['shipment_number' => 'CT-OPERATOR-1'],
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.audit-logs.index', [
            'user_id' => $this->admin->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Carrier');
        $response->assertDontSee('CT-OPERATOR-1');
    }

    /**
     * Test filtering audit logs by action.
     */
    public function test_admin_can_filter_audit_logs_by_action(): void
    {
        AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'deleted',
            'entity_type' => 'Shipment',
            'entity_id' => 50,
        ]);

        AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'status_updated',
            'entity_type' => 'Shipment',
            'entity_id' => 60,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.audit-logs.index', [
            'action' => 'deleted',
        ]));

        $response->assertStatus(200);
        $response->assertSee('#50');
        $response->assertDontSee('#60');
    }

    /**
     * Test filtering audit logs by entity type.
     */
    public function test_admin_can_filter_audit_logs_by_entity_type(): void
    {
        AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'created',
            'entity_type' => 'Carrier',
            'entity_id' => 101,
        ]);

        AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'created',
            'entity_type' => 'Location',
            'entity_id' => 202,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.audit-logs.index', [
            'entity_type' => 'Carrier',
        ]));

        $response->assertStatus(200);
        $response->assertSee('#101');
        $response->assertDontSee('#202');
    }

    /**
     * Test filtering audit logs by date range.
     */
    public function test_admin_can_filter_audit_logs_by_date_range(): void
    {
        $log1 = new AuditLog([
            'user_id' => $this->admin->id,
            'action' => 'created',
            'entity_type' => 'Shipment',
            'entity_id' => 301,
        ]);
        $log1->timestamps = false;
        $log1->created_at = '2026-06-10 12:00:00';
        $log1->save();

        $log2 = new AuditLog([
            'user_id' => $this->admin->id,
            'action' => 'created',
            'entity_type' => 'Shipment',
            'entity_id' => 302,
        ]);
        $log2->timestamps = false;
        $log2->created_at = '2026-08-10 12:00:00';
        $log2->save();

        $response = $this->actingAs($this->admin)->get(route('admin.audit-logs.index', [
            'date_from' => '2026-06-01',
            'date_to' => '2026-06-30',
        ]));

        $response->assertStatus(200);
        $response->assertSee('#301');
        $response->assertDontSee('#302');
    }
}
