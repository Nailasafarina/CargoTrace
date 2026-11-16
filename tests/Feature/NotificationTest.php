<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer1;

    protected User $customer2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer1 = User::factory()->customer()->create();
        $this->customer2 = User::factory()->customer()->create();
    }

    /**
     * Test user can view their notifications page.
     */
    public function test_user_can_view_notifications_index(): void
    {
        Notification::factory()->create([
            'user_id' => $this->customer1->id,
            'type' => 'STATUS_UPDATED',
            'message' => 'Shipment departed Port of Singapore.',
        ]);

        $response = $this->actingAs($this->customer1)->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Notification Center');
        $response->assertSee('Shipment departed Port of Singapore.');
    }

    /**
     * Test customer cannot see other customers' notifications.
     */
    public function test_customer_cannot_view_other_customers_notifications(): void
    {
        Notification::factory()->create([
            'user_id' => $this->customer1->id,
            'type' => 'STATUS_UPDATED',
            'message' => 'Customer 1 secret consignment update.',
        ]);

        Notification::factory()->create([
            'user_id' => $this->customer2->id,
            'type' => 'STATUS_UPDATED',
            'message' => 'Customer 2 private customs alert.',
        ]);

        // Customer 1 visits notification center
        $response = $this->actingAs($this->customer1)->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Customer 1 secret consignment update.');
        $response->assertDontSee('Customer 2 private customs alert.');
    }

    /**
     * Test user can mark an individual notification as read.
     */
    public function test_user_can_mark_notification_as_read(): void
    {
        $notification = Notification::factory()->create([
            'user_id' => $this->customer1->id,
            'read_at' => null,
        ]);

        $this->assertNull($notification->read_at);

        $response = $this->actingAs($this->customer1)
            ->patch(route('notifications.read', $notification));

        $response->assertRedirect();
        $this->assertNotNull($notification->fresh()->read_at);
    }

    /**
     * Test customer cannot mark another customer's notification as read.
     */
    public function test_customer_cannot_mark_another_customers_notification_as_read(): void
    {
        $foreignNotification = Notification::factory()->create([
            'user_id' => $this->customer2->id,
            'read_at' => null,
        ]);

        // Customer 1 attempts to mark Customer 2's notification as read
        $response = $this->actingAs($this->customer1)
            ->patch(route('notifications.read', $foreignNotification));

        $response->assertStatus(403);
        $this->assertNull($foreignNotification->fresh()->read_at);
    }

    /**
     * Test user can mark all their unread notifications as read.
     */
    public function test_user_can_mark_all_notifications_as_read(): void
    {
        Notification::factory()->count(3)->create([
            'user_id' => $this->customer1->id,
            'read_at' => null,
        ]);

        $foreignNotification = Notification::factory()->create([
            'user_id' => $this->customer2->id,
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->customer1)
            ->post(route('notifications.mark-all-read'));

        $response->assertRedirect();

        // Customer 1 has 0 unread notifications left
        $this->assertEquals(0, $this->customer1->notifications()->whereNull('read_at')->count());

        // Customer 2's notification remains unread
        $this->assertNull($foreignNotification->fresh()->read_at);
    }

    /**
     * Test notification unread counter badge displays on navbar.
     */
    public function test_navbar_displays_accurate_unread_counter_badge(): void
    {
        Notification::factory()->count(2)->create([
            'user_id' => $this->customer1->id,
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->customer1)->get(route('customer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('2'); // Badge text
    }
}
