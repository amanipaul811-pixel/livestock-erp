<?php

namespace Tests\Feature\Api;

use App\Models\Animal;
use App\Models\Pen;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class AnimalMovementTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_feeder_can_move_an_animal_to_a_new_pen(): void
    {
        Sanctum::actingAs($this->userWithRole('Feeder'));
        $fromPen = Pen::factory()->create();
        $toPen = Pen::factory()->create();
        $animal = Animal::factory()->create(['current_pen_id' => $fromPen->id]);

        $response = $this->postJson("/api/animals/{$animal->id}/movements", [
            'to_pen_id' => $toPen->id,
            'move_date' => now()->toDateString(),
            'reason' => 'growth stage change',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('animals', ['id' => $animal->id, 'current_pen_id' => $toPen->id]);
        $this->assertDatabaseHas('animal_movements', [
            'animal_id' => $animal->id,
            'from_pen_id' => $fromPen->id,
            'to_pen_id' => $toPen->id,
        ]);
    }

    public function test_moving_to_the_current_pen_is_rejected(): void
    {
        Sanctum::actingAs($this->userWithRole('Feeder'));
        $pen = Pen::factory()->create();
        $animal = Animal::factory()->create(['current_pen_id' => $pen->id]);

        $response = $this->postJson("/api/animals/{$animal->id}/movements", [
            'to_pen_id' => $pen->id,
            'move_date' => now()->toDateString(),
        ]);

        $response->assertStatus(422);
    }

    public function test_sales_role_cannot_move_an_animal(): void
    {
        Sanctum::actingAs($this->userWithRole('Sales'));
        $animal = Animal::factory()->create();
        $toPen = Pen::factory()->create();

        $response = $this->postJson("/api/animals/{$animal->id}/movements", [
            'to_pen_id' => $toPen->id,
            'move_date' => now()->toDateString(),
        ]);

        $response->assertForbidden();
    }
}
