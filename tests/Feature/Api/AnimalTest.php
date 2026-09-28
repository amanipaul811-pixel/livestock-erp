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
            'batch_id' => $batch->id,
            'species_id' => $batch->species_id,
            'sex' => 'male',
            'entry_date' => now()->toDateString(),
            'entry_weight_kg' => 250,
            'purchase_price' => 500,
        ]);

        $response->assertCreated()->assertJsonPath('status', 'on_feed');
        $this->assertMatchesRegularExpression('/^[A-Z]{1,3}-\d{6}$/', $response->json('tag_id'));
    }

    public function test_tag_id_cannot_be_set_by_the_caller_and_is_assigned_sequentially(): void
    {
        Sanctum::actingAs($this->adminUser());
        $batch = Batch::factory()->create();

        $this->postJson('/api/animals', [
            'tag_id' => 'SOMETHING-I-TYPED',
            'batch_id' => $batch->id,
            'species_id' => $batch->species_id,
            'sex' => 'male',
            'entry_date' => now()->toDateString(),
            'entry_weight_kg' => 250,
            'purchase_price' => 500,
        ])->assertCreated();

        $second = $this->postJson('/api/animals', [
            'batch_id' => $batch->id,
            'species_id' => $batch->species_id,
            'sex' => 'female',
            'entry_date' => now()->toDateString(),
            'entry_weight_kg' => 250,
            'purchase_price' => 500,
        ])->assertCreated();

        $this->assertDatabaseMissing('animals', ['tag_id' => 'SOMETHING-I-TYPED']);
        $first = Animal::where('batch_id', $batch->id)->orderBy('id')->first();
        $this->assertNotSame($first->tag_id, $second->json('tag_id'));
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
