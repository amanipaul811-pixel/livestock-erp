<?php

namespace Tests\Unit;

use App\Models\Animal;
use App\Models\AnimalMovement;
use App\Models\HealthRecord;
use App\Models\Pen;
use App\Models\WeighIn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnimalMovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_stay_start_date_falls_back_to_entry_date_with_no_earlier_movement(): void
    {
        $animal = Animal::factory()->create(['entry_date' => now()->subDays(30)]);
        $movement = AnimalMovement::factory()->create(['animal_id' => $animal->id, 'move_date' => now()->subDays(10)]);

        $this->assertTrue($movement->stayStartDate()->isSameDay($animal->entry_date));
    }

    public function test_stay_start_date_uses_the_previous_movements_date(): void
    {
        $animal = Animal::factory()->create(['entry_date' => now()->subDays(60)]);
        $first = AnimalMovement::factory()->create(['animal_id' => $animal->id, 'move_date' => now()->subDays(40)]);
        $second = AnimalMovement::factory()->create(['animal_id' => $animal->id, 'move_date' => now()->subDays(10)]);

        $this->assertTrue($second->stayStartDate()->isSameDay($first->move_date));
    }

    public function test_days_in_pen_is_the_gap_between_stay_start_and_the_move(): void
    {
        $animal = Animal::factory()->create(['entry_date' => now()->subDays(25)]);
        $movement = AnimalMovement::factory()->create(['animal_id' => $animal->id, 'move_date' => now()->subDays(10)]);

        $this->assertSame(15, $movement->daysInPen());
    }

    public function test_health_records_during_stay_only_includes_records_within_the_window(): void
    {
        $animal = Animal::factory()->create(['entry_date' => now()->subDays(30)]);
        $first = AnimalMovement::factory()->create(['animal_id' => $animal->id, 'move_date' => now()->subDays(20)]);
        $second = AnimalMovement::factory()->create(['animal_id' => $animal->id, 'move_date' => now()->subDays(5)]);

        $before = HealthRecord::factory()->create(['animal_id' => $animal->id, 'record_date' => now()->subDays(25)]);
        $during = HealthRecord::factory()->create(['animal_id' => $animal->id, 'record_date' => now()->subDays(12)]);
        $after = HealthRecord::factory()->create(['animal_id' => $animal->id, 'record_date' => now()->subDays(1)]);

        $records = $second->healthRecordsDuringStay();

        $this->assertTrue($records->contains($during));
        $this->assertFalse($records->contains($before));
        $this->assertFalse($records->contains($after));
    }

    public function test_weigh_ins_during_stay_only_includes_weigh_ins_within_the_window(): void
    {
        $animal = Animal::factory()->create(['entry_date' => now()->subDays(30)]);
        $movement = AnimalMovement::factory()->create(['animal_id' => $animal->id, 'move_date' => now()->subDays(5)]);

        $inStay = WeighIn::factory()->create(['animal_id' => $animal->id, 'weigh_date' => now()->subDays(10)]);
        $beforeEntry = WeighIn::factory()->create(['animal_id' => $animal->id, 'weigh_date' => now()->subDays(40)]);

        $weighIns = $movement->weighInsDuringStay();

        $this->assertTrue($weighIns->contains($inStay));
        $this->assertFalse($weighIns->contains($beforeEntry));
    }

    public function test_a_move_with_no_from_pen_has_no_prior_stay(): void
    {
        $animal = Animal::factory()->create(['entry_date' => now()->subDays(5), 'current_pen_id' => null]);
        $pen = Pen::factory()->create();
        $movement = AnimalMovement::factory()->create([
            'animal_id' => $animal->id, 'from_pen_id' => null, 'to_pen_id' => $pen->id, 'move_date' => now(),
        ]);

        $this->assertNull($movement->fromPen);
    }
}
