<?php

namespace Tests\Feature\Api;

use App\Models\Animal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class HealthRecordTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_vet_can_add_a_health_record(): void
    {
        Sanctum::actingAs($this->userWithRole('Vet'));
        $animal = Animal::factory()->create();

        $response = $this->postJson("/api/animals/{$animal->id}/health-records", [
            'record_type' => 'vaccination',
            'record_date' => now()->toDateString(),
            'cost' => 5,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('health_records', ['animal_id' => $animal->id, 'record_type' => 'vaccination']);
    }

    public function test_a_death_record_marks_the_animal_dead(): void
    {
        Sanctum::actingAs($this->userWithRole('Vet'));
        $animal = Animal::factory()->create(['status' => 'on_feed']);

        $this->postJson("/api/animals/{$animal->id}/health-records", [
            'record_type' => 'death',
            'record_date' => now()->toDateString(),
            'cause_of_death' => 'illness',
        ])->assertCreated();

        $this->assertDatabaseHas('animals', ['id' => $animal->id, 'status' => 'dead']);
    }

    public function test_death_record_requires_a_cause(): void
    {
        Sanctum::actingAs($this->userWithRole('Vet'));
        $animal = Animal::factory()->create();

        $response = $this->postJson("/api/animals/{$animal->id}/health-records", [
            'record_type' => 'death',
            'record_date' => now()->toDateString(),
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('cause_of_death');
    }

    public function test_feeder_cannot_add_a_health_record(): void
    {
        Sanctum::actingAs($this->userWithRole('Feeder'));
        $animal = Animal::factory()->create();

        $response = $this->postJson("/api/animals/{$animal->id}/health-records", [
            'record_type' => 'checkup',
            'record_date' => now()->toDateString(),
        ]);

        $response->assertForbidden();
    }
}
