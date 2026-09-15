<?php

namespace Database\Factories;

use App\Models\FeedItem;
use App\Models\FeedStockMovement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FeedStockMovement>
 */
class FeedStockMovementFactory extends Factory
{
    protected $model = FeedStockMovement::class;

    public function definition(): array
    {
        return [
            'feed_item_id' => FeedItem::factory(),
            'type' => 'in',
            'quantity_kg' => 100,
            'occurred_at' => now(),
        ];
    }
}
