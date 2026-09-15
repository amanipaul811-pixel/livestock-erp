<?php

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_a_guest_is_redirected_to_login_from_the_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_login_with_valid_credentials_starts_a_session(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_with_wrong_password_does_not_authenticate(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong',
        ]);

        $this->assertGuest();
    }

    public function test_logout_ends_the_session(): void
    {
        $user = $this->adminUser();

        $this->actingAs($user)->post('/logout');

        $this->assertGuest();
    }
}
