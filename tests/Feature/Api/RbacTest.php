<?php

namespace Tests\Feature\Api;

use App\Models\Species;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_admin_has_every_seeded_permission(): void
    {
        $admin = $this->adminUser();

        $this->assertTrue($admin->hasPermission('batch.create'));
        $this->assertTrue($admin->hasPermission('salesorder.create'));
        $this->assertTrue($admin->hasPermission('purchaseorder.update'));
        $this->assertTrue($admin->hasPermission('user.manage'));
    }

    public function test_a_role_without_a_permission_gets_403_not_a_500(): void
    {
        Sanctum::actingAs($this->userWithRole('Sales'));
        $species = Species::factory()->create();

        $response = $this->postJson('/api/batches', [
            'batch_code' => 'RBAC-1',
            'species_id' => $species->id,
            'start_date' => now()->toDateString(),
        ]);

        $response->assertForbidden()->assertJsonPath('message', 'Missing permission: batch.create');
    }

    public function test_reads_are_open_to_any_authenticated_role_regardless_of_permission(): void
    {
        Sanctum::actingAs($this->userWithRole('Vet'));

        $this->getJson('/api/batches')->assertOk();
    }

    public function test_guests_are_rejected_before_any_permission_check(): void
    {
        $this->postJson('/api/batches', ['batch_code' => 'X'])->assertStatus(401);
    }
}
