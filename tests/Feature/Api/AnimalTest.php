<?php

namespace Tests\Feature\Api;

use App\Models\Animal;
use App\Models\Batch;
use App\Models\WeighIn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class AnimalTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_admin_can_intake_an_animal_into_a_batch(): void
    {
        Sanctum::actingAs($this->adminUser());
        $batch = Batch::factory()->create();

        $response = $this->postJson('/api/animals', [
            'tag_id' => 'TAG-INTAKE-1',
            'batch_id' => $batch->id,
            'species_id' => $batch->species_id,
            'sex' => 'male',
            'entry_date' => now()->toDateString(),
            'entry_weight_kg' => 250,
            'purchase_price' => 500,
        ]);

        $response->assertCreated()->assertJsonPath('status', 'on_feed');
        $this->assertDatabaseHas('animals', ['tag_id' => 'TAG-INTAKE-1', 'status' => 'on_feed']);
    }

    public function test_tag_id_must_be_unique(): void
    {
        Sanctum::actingAs($this->adminUser());
        $batch = Batch::factory()->create();
        Animal::factory()->create(['tag_id' => 'DUP-TAG', 'batch_id' => $batch->id]);

        $response = $this->postJson('/api/animals', [
            'tag_id' => 'DUP-TAG',
            'batch_id' => $batch->id,
            'species_id' => $batch->species_id,
            'sex' => 'male',
            'entry_date' => now()->toDateString(),
            'entry_weight_kg' => 250,
            'purchase_price' => 500,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('tag_id');
    }

    public function test_animal_is_ready_to_sell_once_it_hits_target_weight(): void
    {
        Sanctum::actingAs($this->adminUser());
        $batch = Batch::factory()->create();
        $animal = Animal::factory()->create([
            'batch_id' => $batch->id,
            'species_id' => $batch->species_id,
            'entry_weight_kg' => 250,
        ]);
        WeighIn::factory()->for($animal)->create(['weight_kg' => 400, 'weigh_date' => now()]);

        $response = $this->getJson("/api/animals/{$animal->id}");

        $response->assertOk()->assertJsonPath('ready_to_sell', true);
    }

    public function test_invalid_animal_id_on_weigh_in_returns_404(): void
    {
        Sanctum::actingAs($this->adminUser());

        $response = $this->postJson('/api/animals/99999/weigh-ins', [
            'weigh_date' => now()->toDateString(),
            'weight_kg' => 300,
        ]);

        $response->assertNotFound();
    }
}
