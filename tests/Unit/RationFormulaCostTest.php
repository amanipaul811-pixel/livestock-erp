<?php

namespace Tests\Unit;

use App\Models\FeedItem;
use App\Models\RationFormula;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RationFormulaCostTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_cost_per_head_sums_each_feed_items_contribution(): void
    {
        $formula = RationFormula::factory()->create();
        $maize = FeedItem::factory()->create(['cost_per_unit' => 0.5]);
        $supplement = FeedItem::factory()->create(['cost_per_unit' => 2]);

        $formula->items()->create(['feed_item_id' => $maize->id, 'quantity_kg_per_head' => 8]);
        $formula->items()->create(['feed_item_id' => $supplement->id, 'quantity_kg_per_head' => 1]);

        // (8 * 0.5) + (1 * 2) = 6.0
        $this->assertSame(6.0, $formula->dailyCostPerHead());
    }

    public function test_daily_cost_per_head_is_zero_with_no_items(): void
    {
        $formula = RationFormula::factory()->create();

        $this->assertSame(0.0, $formula->dailyCostPerHead());
    }
}
