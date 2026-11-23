<?php

namespace Tests\Feature;

use App\Enums\DelayStatus;
use App\Enums\ExceptionSeverity;
use App\Enums\ExceptionStatus;
use App\Enums\ExceptionType;
use App\Enums\LocationType;
use App\Enums\Role;
use App\Enums\ShipmentStatus;
use App\Enums\TrackingEventType;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test language switcher changes session locale and redirects.
     */
    public function test_language_switcher_changes_session_locale(): void
    {
        $response = $this->get('/locale/id');

        $response->assertSessionHas('locale', 'id');
        $response->assertStatus(302);

        $responsePost = $this->post('/locale/en');
        $responsePost->assertSessionHas('locale', 'en');
        $responsePost->assertStatus(302);

        $nextResponse = $this->withSession(['locale' => 'en'])->withHeaders(['Accept-Language' => 'id-ID,id;q=0.9'])->get('/login');
        $this->assertEquals('en', app()->getLocale());
        $nextResponse->assertSee('Sign In');
    }

    /**
     * Test navigation preserves chosen locale in html lang attribute and UI strings.
     */
    public function test_navigation_preserves_locale_in_html_lang_and_ui(): void
    {
        // 1. Set locale to English via endpoint
        $res = $this->get('/locale/en');
        $res->assertSessionHas('locale', 'en');
        $res->assertCookie('locale', 'en');

        // 2. Navigate to welcome page
        $welcomeEn = $this->withSession(['locale' => 'en'])->get('/');
        $welcomeEn->assertOk();
        $this->assertEquals('en', app()->getLocale());
        $welcomeEn->assertSee('lang="en"', false);
        $welcomeEn->assertSee('Sign In');

        // 3. Navigate to login page
        $loginEn = $this->withSession(['locale' => 'en'])->get('/login');
        $loginEn->assertOk();
        $this->assertEquals('en', app()->getLocale());
        $loginEn->assertSee('lang="en"', false);
        $loginEn->assertSee('Sign In');

        // 4. Switch to Indonesian
        $resId = $this->get('/locale/id');
        $resId->assertSessionHas('locale', 'id');
        $resId->assertCookie('locale', 'id');

        // 5. Navigate to welcome page in Indonesian
        $welcomeId = $this->withSession(['locale' => 'id'])->get('/');
        $welcomeId->assertOk();
        $this->assertEquals('id', app()->getLocale());
        $welcomeId->assertSee('lang="id"', false);
        $welcomeId->assertSee('Masuk');

        // 6. Navigate to login page in Indonesian
        $loginId = $this->withSession(['locale' => 'id'])->get('/login');
        $loginId->assertOk();
        $this->assertEquals('id', app()->getLocale());
        $loginId->assertSee('lang="id"', false);
        $loginId->assertSee('Masuk');
    }

    /**
     * Test switcher elements rendered in all layouts with proper pointer and z-index styling.
     */
    public function test_switcher_elements_rendered_in_all_layouts(): void
    {
        // Welcome page
        $welcome = $this->get('/');
        $welcome->assertOk();
        $welcome->assertSee(route('locale.set', 'id'));
        $welcome->assertSee(route('locale.set', 'en'));
        $welcome->assertSee('pointer-events-auto', false);

        // Guest page (login)
        $login = $this->get('/login');
        $login->assertOk();
        $login->assertSee(route('locale.set', 'id'));
        $login->assertSee(route('locale.set', 'en'));
        $login->assertSee('pointer-events-auto', false);

        // Authenticated page (admin dashboard)
        $admin = User::factory()->admin()->create();
        $dashboard = $this->actingAs($admin)->get('/admin/dashboard');
        $dashboard->assertOk();
        $dashboard->assertSee(route('locale.set', 'id'));
        $dashboard->assertSee(route('locale.set', 'en'));
        $dashboard->assertSee('pointer-events-auto', false);
    }

    /**
     * Test invalid locale returns 400 Bad Request.
     */
    public function test_invalid_locale_returns_bad_request(): void
    {
        $response = $this->get('/locale/fr');
        $response->assertStatus(400);

        $responsePost = $this->post('/locale/es');
        $responsePost->assertStatus(400);
    }

    /**
     * Test switching locale updates authenticated user record in database.
     */
    public function test_switching_locale_updates_authenticated_user_profile(): void
    {
        $user = User::factory()->create([
            'locale' => 'en',
        ]);

        $this->actingAs($user)->get('/locale/id');

        $this->assertEquals('id', $user->fresh()->locale);
        $this->assertEquals('id', session('locale'));
    }

    /**
     * Test user preserves chosen locale across login and logout.
     */
    public function test_user_preserves_locale_across_sessions(): void
    {
        $user = User::factory()->create([
            'email' => 'operator_id@cargotrace.test',
            'password' => bcrypt('Password123!'),
            'locale' => 'id',
        ]);

        // Login with empty session
        $this->post('/login', [
            'email' => 'operator_id@cargotrace.test',
            'password' => 'Password123!',
        ]);

        $this->assertAuthenticatedAs($user);
        $this->assertEquals('id', session('locale'));

        // Logout
        $this->post('/logout');
        $this->assertGuest();

        // Login again
        $this->post('/login', [
            'email' => 'operator_id@cargotrace.test',
            'password' => 'Password123!',
        ]);

        $this->assertAuthenticatedAs($user);
        $this->assertEquals('id', session('locale'));
    }

    /**
     * Test API header Accept-Language modifies unauthenticated error message.
     */
    public function test_api_accept_language_header_changes_unauthenticated_message(): void
    {
        $responseEn = $this->withHeaders([
            'Accept' => 'application/json',
            'Accept-Language' => 'en',
        ])->getJson('/api/shipments');

        $responseEn->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated. A valid Sanctum bearer token is required.',
            ]);

        $responseId = $this->withHeaders([
            'Accept' => 'application/json',
            'Accept-Language' => 'id',
        ])->getJson('/api/shipments');

        $responseId->assertStatus(401)
            ->assertJson([
                'message' => 'Tidak terautentikasi. Token bearer Sanctum yang valid diperlukan.',
            ]);
    }

    /**
     * Test API header Accept-Language modifies 404 not found error message.
     */
    public function test_api_accept_language_header_changes_not_found_message(): void
    {
        $user = User::factory()->admin()->create();

        $responseEn = $this->actingAs($user, 'sanctum')->withHeaders([
            'Accept' => 'application/json',
            'Accept-Language' => 'en',
        ])->getJson('/api/shipments/999999');

        $responseEn->assertStatus(404)
            ->assertJson([
                'message' => 'The requested resource was not found.',
            ]);

        $responseId = $this->actingAs($user, 'sanctum')->withHeaders([
            'Accept' => 'application/json',
            'Accept-Language' => 'id',
        ])->getJson('/api/shipments/999999');

        $responseId->assertStatus(404)
            ->assertJson([
                'message' => 'Sumber daya yang diminta tidak ditemukan.',
            ]);
    }

    /**
     * Test API header Accept-Language modifies 403 forbidden error message.
     */
    public function test_api_accept_language_header_changes_forbidden_message(): void
    {
        $customer = User::factory()->customer()->create();

        // Customer attempts to create carrier via API (requires admin/operator)
        $responseEn = $this->actingAs($customer, 'sanctum')->withHeaders([
            'Accept' => 'application/json',
            'Accept-Language' => 'en',
        ])->postJson('/api/carriers', [
            'name' => 'Unauthorized Carrier',
            'code' => 'UNAUTH1',
        ]);

        $responseEn->assertStatus(403)
            ->assertJson([
                'message' => 'Forbidden. You do not have permission to access or modify this resource.',
            ]);

        $responseId = $this->actingAs($customer, 'sanctum')->withHeaders([
            'Accept' => 'application/json',
            'Accept-Language' => 'id',
        ])->postJson('/api/carriers', [
            'name' => 'Unauthorized Carrier',
            'code' => 'UNAUTH2',
        ]);

        $responseId->assertStatus(403)
            ->assertJson([
                'message' => 'Dilarang. Anda tidak memiliki izin untuk mengakses atau mengubah sumber daya ini.',
            ]);
    }

    /**
     * Test Enum label() methods return proper bilingual translations.
     */
    public function test_enum_labels_change_according_to_locale(): void
    {
        // Test in English
        App::setLocale('en');

        $this->assertEquals('In Transit', ShipmentStatus::InTransit->label());
        $this->assertEquals('Delivered', ShipmentStatus::Delivered->label());
        $this->assertEquals('Critical Delay', DelayStatus::CriticalDelay->label());
        $this->assertEquals('On Time', DelayStatus::OnTime->label());
        $this->assertEquals('Customs Regulatory Hold', ExceptionType::CustomsHold->label());
        $this->assertEquals('Open', ExceptionStatus::Open->label());
        $this->assertEquals('High', ExceptionSeverity::High->label());
        $this->assertEquals('Administrator', Role::Admin->label());
        $this->assertEquals('Logistics Operator', Role::Operator->label());
        $this->assertEquals('Port', LocationType::Port->label());
        $this->assertEquals('Customs Cleared', TrackingEventType::CustomsCleared->label());

        // Test in Indonesian
        App::setLocale('id');

        $this->assertEquals('Dalam Perjalanan', ShipmentStatus::InTransit->label());
        $this->assertEquals('Terkirim', ShipmentStatus::Delivered->label());
        $this->assertEquals('Keterlambatan Kritis', DelayStatus::CriticalDelay->label());
        $this->assertEquals('Tepat Waktu', DelayStatus::OnTime->label());
        $this->assertEquals('Penahanan Bea Cukai', ExceptionType::CustomsHold->label());
        $this->assertEquals('Terbuka', ExceptionStatus::Open->label());
        $this->assertEquals('Tinggi', ExceptionSeverity::High->label());
        $this->assertEquals('Administrator', Role::Admin->label());
        $this->assertEquals('Operator Logistik', Role::Operator->label());
        $this->assertEquals('Pelabuhan', LocationType::Port->label());
        $this->assertEquals('Lolos Bea Cukai', TrackingEventType::CustomsCleared->label());
    }

    /**
     * Test in-app notification message accessor renders localized string from JSON payload.
     */
    public function test_notification_message_renders_per_locale(): void
    {
        $user = User::factory()->create();

        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'status_change',
            'message' => json_encode([
                'key' => 'notifications.messages.shipment_milestone_updated',
                'params' => [
                    'number' => 'CT-TEST-001',
                    'status' => 'IN_TRANSIT',
                    'description' => 'Container loaded at terminal',
                ],
            ]),
            'read_at' => null,
        ]);

        App::setLocale('en');
        $this->assertStringContainsString('Shipment CT-TEST-001 status updated to In Transit: Container loaded at terminal', $notification->message);

        App::setLocale('id');
        $this->assertStringContainsString('Status pengiriman CT-TEST-001 diperbarui ke Dalam Perjalanan: Container loaded at terminal', $notification->message);
    }

    /**
     * Test shipment delay_reason accessor renders localized message.
     */
    public function test_shipment_delay_reason_renders_per_locale(): void
    {
        $shipment = Shipment::factory()->create([
            'shipment_number' => 'CT-DELAY-01',
            'current_status' => ShipmentStatus::InTransit,
            'delay_status' => DelayStatus::High,
            'delay_reason' => json_encode([
                'key' => 'shipments.delay_reasons.exceeded_threshold',
                'params' => [
                    'estimated' => '15 Nov 14:00',
                    'expected' => '14 Nov 10:00',
                    'hours' => 28,
                    'threshold' => 24,
                    'classification' => 'HIGH',
                ],
            ]),
        ]);

        App::setLocale('en');
        $this->assertStringContainsString('ETA 15 Nov 14:00 exceeds expected 14 Nov 10:00 by 28 hours; threshold 24 hours → HIGH', $shipment->delay_reason);

        App::setLocale('id');
        $this->assertStringContainsString('ETA 15 Nov 14:00 melebihi expected 14 Nov 10:00 sebesar 28 jam; threshold 24 jam → HIGH', $shipment->delay_reason);
    }

    /**
     * Test language parity command returns success code.
     */
    public function test_language_parity_command_succeeds(): void
    {
        $this->artisan('lang:check-parity')
            ->assertExitCode(0);
    }
}
