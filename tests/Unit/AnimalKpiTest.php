<?php

namespace Tests\Unit;

use App\Models\Animal;
use App\Models\Species;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnimalKpiTest extends TestCase
{
    use RefreshDatabase;

    public function test_latest_weight_falls_back_to_entry_weight_with_no_weigh_ins(): void
    {
        $animal = Animal::factory()->create(['entry_weight_kg' => 250]);

        $this->assertSame(250.0, $animal->latestWeightKg());
    }

    public function test_latest_weight_uses_the_most_recent_weigh_in(): void
    {
        $animal = Animal::factory()->create(['entry_weight_kg' => 250]);
        $animal->weighIns()->create(['weigh_date' => now()->subDays(10), 'weight_kg' => 300]);
        $animal->weighIns()->create(['weigh_date' => now(), 'weight_kg' => 350]);

        $this->assertSame(350.0, $animal->latestWeightKg());
    }

    public function test_current_weight_gain_is_latest_minus_entry(): void
    {
        $animal = Animal::factory()->create(['entry_weight_kg' => 250]);
        $animal->weighIns()->create(['weigh_date' => now(), 'weight_kg' => 300]);

        $this->assertSame(50.0, $animal->currentWeightGainKg());
    }

    public function test_average_daily_gain_divides_gain_by_days_since_entry(): void
    {
        $animal = Animal::factory()->create([
            'entry_date' => now()->subDays(10),
            'entry_weight_kg' => 250,
        ]);
        $animal->weighIns()->create(['weigh_date' => now(), 'weight_kg' => 300]);

        $this->assertSame(5.0, $animal->averageDailyGainKg());
    }

    public function test_ready_to_sell_is_false_below_the_species_target(): void
    {
        $species = Species::factory()->create(['target_exit_weight_kg' => 400]);
        $animal = Animal::factory()->create(['species_id' => $species->id, 'entry_weight_kg' => 250]);

        $this->assertFalse($animal->isReadyToSell());
    }

    public function test_ready_to_sell_is_true_at_or_above_the_species_target(): void
    {
        $species = Species::factory()->create(['target_exit_weight_kg' => 400]);
        $animal = Animal::factory()->create(['species_id' => $species->id, 'entry_weight_kg' => 250]);
        $animal->weighIns()->create(['weigh_date' => now(), 'weight_kg' => 400]);

        $this->assertTrue($animal->isReadyToSell());
    }

    public function test_ready_to_sell_is_false_when_species_has_no_target(): void
    {
        $species = Species::factory()->create(['target_exit_weight_kg' => null]);
        $animal = Animal::factory()->create(['species_id' => $species->id, 'entry_weight_kg' => 250]);
        $animal->weighIns()->create(['weigh_date' => now(), 'weight_kg' => 9999]);

        $this->assertFalse($animal->isReadyToSell());
    }
}
