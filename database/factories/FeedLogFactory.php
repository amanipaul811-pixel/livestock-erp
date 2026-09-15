<?php

namespace Database\Factories;

use App\Models\Batch;
use App\Models\FeedItem;
use App\Models\FeedLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FeedLog>
 */
class FeedLogFactory extends Factory
{
    protected $model = FeedLog::class;

    public function definition(): array
    {
        return [
            'batch_id' => Batch::factory(),
            'feed_item_id' => FeedItem::factory(),
            'feed_date' => now(),
            'quantity_kg' => 10,
            'total_cost' => 5,
        ];
    }
}
