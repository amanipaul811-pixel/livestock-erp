<?php

namespace Database\Factories;

use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Species>
 */
class SpeciesFactory extends Factory
{
    protected $model = Species::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'default_cycle_days' => 120,
            'target_adg_kg' => 1.2,
            'target_entry_weight_kg' => 250,
            'target_exit_weight_kg' => 400,
        ];
    }
}
