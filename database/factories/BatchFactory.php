<?php

namespace Database\Factories;

use App\Models\Batch;
use App\Models\Pen;
use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Batch>
 */
class BatchFactory extends Factory
{
    protected $model = Batch::class;

    public function definition(): array
    {
        return [
            'batch_code' => fake()->unique()->bothify('B-####'),
            'species_id' => Species::factory(),
            'pen_id' => Pen::factory(),
            'start_date' => now()->subDays(30),
            'expected_end_date' => now()->addDays(90),
            'status' => 'active',
        ];
    }
}
