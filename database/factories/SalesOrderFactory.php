<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\SalesOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalesOrder>
 */
class SalesOrderFactory extends Factory
{
    protected $model = SalesOrder::class;

    public function definition(): array
    {
        return [
            'so_number' => 'SO-'.fake()->unique()->numerify('########'),
            'customer_id' => Customer::factory(),
            'sale_date' => now(),
            'status' => 'completed',
            'total_amount' => 1000,
        ];
    }
}
