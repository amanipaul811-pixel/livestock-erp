<?php

namespace Database\Factories;

use App\Models\Animal;
use App\Models\WeighIn;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WeighIn>
 */
class WeighInFactory extends Factory
{
    protected $model = WeighIn::class;

    public function definition(): array
    {
        return [
            'animal_id' => Animal::factory(),
            'weigh_date' => now(),
            'weight_kg' => 300,
        ];
    }
}
