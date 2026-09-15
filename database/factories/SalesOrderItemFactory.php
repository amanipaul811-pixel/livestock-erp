<?php

namespace Database\Factories;

use App\Models\Animal;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalesOrderItem>
 */
class SalesOrderItemFactory extends Factory
{
    protected $model = SalesOrderItem::class;

    public function definition(): array
    {
        return [
            'sales_order_id' => SalesOrder::factory(),
            'animal_id' => Animal::factory(),
            'sale_weight_kg' => 400,
            'price_per_kg' => 3.5,
            'line_total' => 1400,
        ];
    }
}
