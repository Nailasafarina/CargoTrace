<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAuditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test public self-registration cannot escalate privileges to admin or operator.
     */
    public function test_public_api_registration_cannot_escalate_role_to_admin(): void
    {
        $payload = [
            'name' => 'Hacker Admin',
            'email' => 'hacker@cargotrace.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'admin',
        ];

        $response = $this->postJson('/api/register', $payload);

        // Validation rejects unauthorized role escalation with 422
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);

        $this->assertDatabaseMissing('users', [
            'email' => 'hacker@cargotrace.test',
            'role' => 'admin',
        ]);
    }

    /**
     * Test web registration strictly forces role to customer even if role is tampered in payload.
     */
    public function test_web_registration_strictly_forces_customer_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Web Exploiter',
            'email' => 'exploiter@cargotrace.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('customer.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'exploiter@cargotrace.test',
            'role' => 'customer',
        ]);
        $this->assertDatabaseMissing('users', [
            'email' => 'exploiter@cargotrace.test',
            'role' => 'admin',
        ]);
    }

    /**
     * Test XSS payloads in tracking event description and location name are safely escaped.
     */
    public function test_xss_payloads_are_safely_escaped_in_shipment_view(): void
    {
        $operator = User::factory()->operator()->create();
        $carrier = Carrier::factory()->create();
        $origin = Location::factory()->create([
            'name' => 'Port <script>alert("xss-origin")</script>',
            'latitude' => 1.2902,
            'longitude' => 103.8519,
        ]);
        $destination = Location::factory()->create([
            'name' => 'Port <img src=x onerror=alert("xss-dest")>',
            'latitude' => 51.9244,
            'longitude' => 4.4777,
        ]);

        $shipment = Shipment::factory()->create([
            'carrier_id' => $carrier->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
            'cargo_type' => 'Standard Cargo',
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'location_id' => $origin->id,
            'event_type' => 'PICKED_UP',
            'is_exception' => false,
            'occurred_at' => now(),
            'latitude' => 1.2902,
            'longitude' => 103.8519,
            'description' => '<b onmouseover=alert("xss-desc")>Tampered</b>',
            'created_by' => $operator->id,
        ]);

        $response = $this->actingAs($operator)->get(route('shipments.show', $shipment));

        $response->assertStatus(200);

        // Raw executable tags must NOT be present unescaped in HTML
        $response->assertDontSee('<script>alert("xss-origin")</script>', false);
        $response->assertDontSee('<img src=x onerror=alert("xss-dest")>', false);
        $response->assertDontSee('<b onmouseover=alert("xss-desc")>', false);

        // Must contain JavaScript DOM XSS sanitization helper
        $response->assertSee('escapeHtml', false);
    }

    /**
     * Test SQL Injection payloads in shipment search and sorting are neutralized.
     */
    public function test_sql_injection_payloads_in_search_and_sorting_are_neutralized(): void
    {
        $operator = User::factory()->operator()->create();

        // 1. SQL Injection attempt in search query
        $sqlSearchPayload = "CT-' OR '1'='1' --";
        $response = $this->actingAs($operator)->get(route('shipments.index', [
            'search' => $sqlSearchPayload,
        ]));
        $response->assertStatus(200);

        // 2. SQL Injection attempt in sort_by parameter (bypassed through whitelist fallback)
        $sqlSortPayload = 'id; DROP TABLE shipments; --';
        $responseSort = $this->actingAs($operator)->get(route('shipments.index', [
            'sort_by' => $sqlSortPayload,
            'sort_dir' => 'desc',
        ]));
        $responseSort->assertStatus(200);

        // Verify shipments table still intact and accessible
        $this->assertDatabaseCount('shipments', 0);
    }

    /**
     * Test API error responses do not leak stack traces when APP_DEBUG=false.
     */
    public function test_api_does_not_leak_stack_traces_when_debug_is_disabled(): void
    {
        config(['app.debug' => false]);

        $user = User::factory()->customer()->create();

        // Request non-existent shipment resource
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/shipments/999999');

        $response->assertStatus(404);
        $response->assertJsonStructure(['message']);
        $response->assertDontSee('trace', false);
        $response->assertDontSee('Exception', false);
        $response->assertDontSee('vendor/laravel', false);
    }
}
