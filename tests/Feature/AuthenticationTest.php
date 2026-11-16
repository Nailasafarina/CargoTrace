<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the login screen can be rendered.
     */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Sign in to CargoTrace');
    }

    /**
     * Test that users can authenticate with valid credentials.
     */
    public function test_users_can_authenticate_with_valid_credentials(): void
    {
        $user = User::factory()->admin()->create([
            'email' => 'admin@cargotrace.test',
            'password' => bcrypt('Password123!'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@cargotrace.test',
            'password' => 'Password123!',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Test that users cannot authenticate with invalid password.
     */
    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'customer@cargotrace.test',
            'password' => bcrypt('CorrectPassword123!'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'customer@cargotrace.test',
            'password' => 'WrongPassword!',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
        $response->assertRedirect('/login');
    }

    /**
     * Test that new users can register and get assigned customer role.
     */
    public function test_new_users_can_register_as_customer(): void
    {
        $response = $this->post('/register', [
            'name' => 'New Logistics Client',
            'email' => 'client@company.test',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $this->assertAuthenticated();

        $user = User::where('email', 'client@company.test')->first();
        $this->assertNotNull($user);
        $this->assertEquals('customer', $user->role);
        $this->assertTrue($user->isCustomer());

        $response->assertRedirect(route('customer.dashboard'));
    }

    /**
     * Test that authenticated users can log out.
     */
    public function test_authenticated_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }
}
