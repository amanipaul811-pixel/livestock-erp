<?php

namespace Tests\Unit;

use App\Models\FeedItem;
use App\Models\FeedStockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedItemStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_stock_nets_in_out_and_adjustment_movements(): void
    {
        $feedItem = FeedItem::factory()->create();
        FeedStockMovement::factory()->create(['feed_item_id' => $feedItem->id, 'type' => 'in', 'quantity_kg' => 200]);
        FeedStockMovement::factory()->create(['feed_item_id' => $feedItem->id, 'type' => 'out', 'quantity_kg' => 50]);
        FeedStockMovement::factory()->create(['feed_item_id' => $feedItem->id, 'type' => 'adjustment', 'quantity_kg' => -10]);

        $this->assertSame(140.0, $feedItem->currentStock());
    }

    public function test_is_low_stock_when_at_or_below_reorder_level(): void
    {
        $feedItem = FeedItem::factory()->create(['reorder_level' => 100]);
        FeedStockMovement::factory()->create(['feed_item_id' => $feedItem->id, 'type' => 'in', 'quantity_kg' => 100]);

        $this->assertTrue($feedItem->isLowStock());
    }

    public function test_is_not_low_stock_above_reorder_level(): void
    {
        $feedItem = FeedItem::factory()->create(['reorder_level' => 100]);
        FeedStockMovement::factory()->create(['feed_item_id' => $feedItem->id, 'type' => 'in', 'quantity_kg' => 150]);

        $this->assertFalse($feedItem->isLowStock());
    }

    public function test_is_not_low_stock_when_reorder_level_is_zero(): void
    {
        $feedItem = FeedItem::factory()->create(['reorder_level' => 0]);

        $this->assertFalse($feedItem->isLowStock());
    }
}
