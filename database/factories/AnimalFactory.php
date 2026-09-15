<?php

namespace Database\Factories;

use App\Models\Animal;
use App\Models\Batch;
use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Animal>
 */
class AnimalFactory extends Factory
{
    protected $model = Animal::class;

    public function definition(): array
    {
        return [
            'tag_id' => fake()->unique()->bothify('TAG-####'),
            'batch_id' => Batch::factory(),
            'species_id' => Species::factory(),
            'sex' => fake()->randomElement(['male', 'female']),
            'entry_date' => now()->subDays(30),
            'entry_weight_kg' => 250,
            'purchase_price' => 500,
            'status' => 'on_feed',
        ];
    }
}
