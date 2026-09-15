<?php

namespace Database\Factories;

use App\Models\Animal;
use App\Models\AnimalMovement;
use App\Models\Pen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnimalMovement>
 */
class AnimalMovementFactory extends Factory
{
    protected $model = AnimalMovement::class;

    public function definition(): array
    {
        return [
            'animal_id' => Animal::factory(),
            'to_pen_id' => Pen::factory(),
            'move_date' => now(),
        ];
    }
}
