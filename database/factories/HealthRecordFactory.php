<?php

namespace Database\Factories;

use App\Models\Animal;
use App\Models\HealthRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HealthRecord>
 */
class HealthRecordFactory extends Factory
{
    protected $model = HealthRecord::class;

    public function definition(): array
    {
        return [
            'animal_id' => Animal::factory(),
            'record_type' => 'checkup',
            'record_date' => now(),
            'cost' => 0,
        ];
    }
}
