<?php

namespace Database\Factories;

use App\Models\FeedItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FeedItem>
 */
class FeedItemFactory extends Factory
{
    protected $model = FeedItem::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'unit' => 'kg',
            'cost_per_unit' => 0.5,
            'reorder_level' => 0,
        ];
    }
}
