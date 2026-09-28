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
            'species_id' => $species->id,
            'pen_id' => $pen->id,
            'start_date' => now()->toDateString(),
        ]);

        $response->assertCreated()->assertJsonPath('status', 'active');
        $this->assertMatchesRegularExpression('/^B-\d{4}-\d{4}$/', $response->json('batch_code'));
    }

    public function test_batch_code_cannot_be_set_by_the_caller_and_is_assigned_sequentially(): void
    {
        Sanctum::actingAs($this->adminUser());
        $species = Species::factory()->create();

        $this->postJson('/api/batches', [
            'batch_code' => 'SOMETHING-I-TYPED',
            'species_id' => $species->id,
            'start_date' => now()->toDateString(),
        ])->assertCreated();

        $second = $this->postJson('/api/batches', [
            'species_id' => $species->id,
            'start_date' => now()->toDateString(),
        ])->assertCreated();

        $this->assertDatabaseMissing('batches', ['batch_code' => 'SOMETHING-I-TYPED']);
        $first = \App\Models\Batch::where('species_id', $species->id)->orderBy('id')->first();
        $this->assertNotSame($first->batch_code, $second->json('batch_code'));
    }

    public function test_vet_cannot_create_a_batch(): void
    {
        Sanctum::actingAs($this->userWithRole('Vet'));
        $species = Species::factory()->create();

        $response = $this->postJson('/api/batches', [
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
