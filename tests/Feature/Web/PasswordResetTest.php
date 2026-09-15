<?php

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_requesting_a_reset_link_queues_a_notification_for_a_real_user(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_requesting_a_reset_link_for_an_unknown_email_still_reports_success(): void
    {
        // Must not leak whether an email has an account.
        $response = $this->post('/forgot-password', ['email' => 'nobody@example.com']);

        $response->assertSessionHas('status');
    }

    public function test_a_valid_token_resets_the_password(): void
    {
        Notification::fake();
        $user = User::factory()->create(['password' => Hash::make('OldPassword1')]);

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'BrandNewPassword1',
                'password_confirmation' => 'BrandNewPassword1',
            ]);

            $response->assertRedirect(route('login'));

            return true;
        });

        $this->assertTrue(Hash::check('BrandNewPassword1', $user->fresh()->password));
    }

    public function test_a_weak_password_is_rejected_on_reset(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'alllowercase',
                'password_confirmation' => 'alllowercase',
            ]);

            $response->assertSessionHasErrors('password');

            return true;
        });
    }

    public function test_the_login_route_is_rate_limited_after_five_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/login', ['email' => 'nobody@example.com', 'password' => 'wrong'])
                ->assertStatus(422);
        }

        $this->postJson('/api/login', ['email' => 'nobody@example.com', 'password' => 'wrong'])
            ->assertStatus(429);
    }

    public function test_the_rate_limit_is_keyed_per_account_not_globally(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $this->postJson('/api/login', ['email' => 'locked-out@example.com', 'password' => 'wrong']);
        }

        // A different account from the same test client should be unaffected.
        $user = User::factory()->create(['password' => Hash::make('CorrectPassword1')]);

        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'CorrectPassword1'])
            ->assertOk();
    }
}
