<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guests accessing protected role routes are redirected to login.
     */
    public function test_unauthenticated_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/operator/dashboard')->assertRedirect('/login');
        $this->get('/customer/dashboard')->assertRedirect('/login');
    }

    /**
     * Test that an administrator can access admin dashboard and is redirected correctly.
     */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertStatus(200)
            ->assertSee('Administrator Console')
            ->assertSee('role: admin');

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Test that an administrator is forbidden from accessing operator/customer specific dashboards.
     */
    public function test_admin_cannot_access_other_role_dashboards(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/operator/dashboard')
            ->assertStatus(403);

        $this->actingAs($admin)
            ->get('/customer/dashboard')
            ->assertStatus(403);
    }

    /**
     * Test that a logistics operator can access operator dashboard.
     */
    public function test_operator_can_access_operator_dashboard(): void
    {
        $operator = User::factory()->operator()->create();

        $this->actingAs($operator)
            ->get('/operator/dashboard')
            ->assertStatus(200)
            ->assertSee('Logistics Operations Control')
            ->assertSee('role: operator');

        $this->actingAs($operator)
            ->get('/dashboard')
            ->assertRedirect(route('operator.dashboard'));
    }

    /**
     * Test that a logistics operator cannot access admin or customer dashboards.
     */
    public function test_operator_cannot_access_other_role_dashboards(): void
    {
        $operator = User::factory()->operator()->create();

        $this->actingAs($operator)
            ->get('/admin/dashboard')
            ->assertStatus(403);

        $this->actingAs($operator)
            ->get('/customer/dashboard')
            ->assertStatus(403);
    }

    /**
     * Test that a customer can access customer dashboard.
     */
    public function test_customer_can_access_customer_dashboard(): void
    {
        $customer = User::factory()->customer()->create();

        $this->actingAs($customer)
            ->get('/customer/dashboard')
            ->assertStatus(200)
            ->assertSee('Customer Tracking Portal')
            ->assertSee('role: customer');

        $this->actingAs($customer)
            ->get('/dashboard')
            ->assertRedirect(route('customer.dashboard'));
    }

    /**
     * Test that a customer cannot access admin or operator dashboards.
     */
    public function test_customer_cannot_access_other_role_dashboards(): void
    {
        $customer = User::factory()->customer()->create();

        $this->actingAs($customer)
            ->get('/admin/dashboard')
            ->assertStatus(403);

        $this->actingAs($customer)
            ->get('/operator/dashboard')
            ->assertStatus(403);
    }
}
