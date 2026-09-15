<?php

namespace Database\Factories;

use App\Models\RationFormula;
use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RationFormula>
 */
class RationFormulaFactory extends Factory
{
    protected $model = RationFormula::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'species_id' => Species::factory(),
            'stage' => 'growing',
        ];
    }
}
