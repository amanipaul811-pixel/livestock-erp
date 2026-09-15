<?php

namespace Tests\Feature\Api;

use App\Models\Pen;
use App\Models\Species;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class BatchTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_admin_can_create_a_batch(): void
    {
        Sanctum::actingAs($this->adminUser());
        $species = Species::factory()->create();
        $pen = Pen::factory()->create();

        $response = $this->postJson('/api/batches', [
            'batch_code' => 'B-TEST-1',
            'species_id' => $species->id,
            'pen_id' => $pen->id,
            'start_date' => now()->toDateString(),
        ]);

        $response->assertCreated()->assertJsonPath('status', 'active');
        $this->assertDatabaseHas('batches', ['batch_code' => 'B-TEST-1']);
    }

    public function test_batch_code_must_be_unique(): void
    {
        Sanctum::actingAs($this->adminUser());
        $species = Species::factory()->create();
        \App\Models\Batch::factory()->create(['batch_code' => 'DUPLICATE', 'species_id' => $species->id]);

        $response = $this->postJson('/api/batches', [
            'batch_code' => 'DUPLICATE',
            'species_id' => $species->id,
            'start_date' => now()->toDateString(),
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('batch_code');
    }

    public function test_vet_cannot_create_a_batch(): void
    {
        Sanctum::actingAs($this->userWithRole('Vet'));
        $species = Species::factory()->create();

        $response = $this->postJson('/api/batches', [
            'batch_code' => 'B-TEST-2',
            'species_id' => $species->id,
            'start_date' => now()->toDateString(),
        ]);

        $response->assertForbidden();
    }

    public function test_invalid_batch_id_returns_404(): void
    {
        Sanctum::actingAs($this->adminUser());

        $this->getJson('/api/batches/99999')->assertNotFound();
    }
}
