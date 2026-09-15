<?php

namespace Tests\Feature\Web;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_an_admin_can_create_a_new_staff_user(): void
    {
        $admin = $this->adminUser();
        $role = Role::where('name', 'Feeder')->firstOrFail();

        $response = $this->actingAs($admin)->post('/users', [
            'full_name' => 'New Feeder',
            'email' => 'new-feeder@example.com',
            'password' => 'password123',
            'role_id' => $role->id,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['email' => 'new-feeder@example.com', 'role_id' => $role->id]);
    }

    public function test_a_user_can_be_updated_without_changing_the_password(): void
    {
        $admin = $this->adminUser();
        $user = User::factory()->create(['password' => Hash::make('original-password')]);

        $this->actingAs($admin)->put("/users/{$user->id}", [
            'full_name' => 'Renamed User',
            'email' => $user->email,
            'role_id' => $user->role_id,
            'is_active' => '1',
        ])->assertRedirect(route('users.index'));

        $user->refresh();
        $this->assertSame('Renamed User', $user->full_name);
        $this->assertTrue(Hash::check('original-password', $user->password));
    }

    public function test_a_user_password_changes_when_a_new_one_is_provided(): void
    {
        $admin = $this->adminUser();
        $user = User::factory()->create(['password' => Hash::make('original-password')]);

        $this->actingAs($admin)->put("/users/{$user->id}", [
            'full_name' => $user->full_name,
            'email' => $user->email,
            'password' => 'brand-new-password',
            'role_id' => $user->role_id,
            'is_active' => '1',
        ]);

        $this->assertTrue(Hash::check('brand-new-password', $user->fresh()->password));
    }

    public function test_a_feeder_cannot_reach_user_management(): void
    {
        $feeder = $this->userWithRole('Feeder');

        $this->actingAs($feeder)->get('/users')->assertForbidden();
        $this->actingAs($feeder)->get('/users/create')->assertForbidden();
    }

    public function test_the_users_nav_link_is_hidden_from_roles_without_user_manage(): void
    {
        $response = $this->actingAs($this->userWithRole('Feeder'))->get('/dashboard');

        $response->assertOk()->assertDontSee(route('users.index'), false);
    }
}
