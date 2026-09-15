<?php

namespace Database\Factories;

use App\Models\Pen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pen>
 */
class PenFactory extends Factory
{
    protected $model = Pen::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->bothify('Pen ##'),
            'capacity' => 50,
            'stage' => 'growing',
            'is_active' => true,
        ];
    }
}
