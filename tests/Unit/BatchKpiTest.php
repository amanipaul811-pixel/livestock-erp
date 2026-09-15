<?php

namespace Tests\Unit;

use App\Models\Animal;
use App\Models\Batch;
use App\Models\Expense;
use App\Models\FeedLog;
use App\Models\HealthRecord;
use App\Models\SalesOrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BatchKpiTest extends TestCase
{
    use RefreshDatabase;

    public function test_days_on_feed_counts_from_start_date(): void
    {
        $batch = Batch::factory()->create(['start_date' => now()->subDays(10)]);

        $this->assertSame(10, $batch->daysOnFeed());
    }

    public function test_total_purchase_cost_sums_animal_purchase_prices(): void
    {
        $batch = Batch::factory()->create();
        Animal::factory()->create(['batch_id' => $batch->id, 'purchase_price' => 500]);
        Animal::factory()->create(['batch_id' => $batch->id, 'purchase_price' => 300]);

        $this->assertSame(800.0, $batch->totalPurchaseCost());
    }

    public function test_total_feed_cost_sums_feed_logs(): void
    {
        $batch = Batch::factory()->create();
        FeedLog::factory()->create(['batch_id' => $batch->id, 'total_cost' => 5]);
        FeedLog::factory()->create(['batch_id' => $batch->id, 'total_cost' => 7.5]);

        $this->assertSame(12.5, $batch->totalFeedCost());
    }

    public function test_total_health_cost_sums_health_records_for_the_batchs_animals(): void
    {
        $batch = Batch::factory()->create();
        $animal = Animal::factory()->create(['batch_id' => $batch->id]);
        HealthRecord::factory()->create(['animal_id' => $animal->id, 'cost' => 5]);
        HealthRecord::factory()->create(['animal_id' => $animal->id, 'cost' => 3]);

        $this->assertSame(8.0, $batch->totalHealthCost());
    }

    public function test_total_other_expenses_sums_expenses(): void
    {
        $batch = Batch::factory()->create();
        Expense::factory()->create(['batch_id' => $batch->id, 'amount' => 40]);

        $this->assertSame(40.0, $batch->totalOtherExpenses());
    }

    public function test_total_sales_revenue_sums_line_totals_for_the_batchs_animals(): void
    {
        $batch = Batch::factory()->create();
        $animal = Animal::factory()->create(['batch_id' => $batch->id]);
        SalesOrderItem::factory()->create(['animal_id' => $animal->id, 'line_total' => 1400]);

        $this->assertSame(1400.0, $batch->totalSalesRevenue());
    }

    public function test_net_profit_combines_all_the_kpis(): void
    {
        $batch = Batch::factory()->create();
        $animal = Animal::factory()->create(['batch_id' => $batch->id, 'purchase_price' => 500]);
        FeedLog::factory()->create(['batch_id' => $batch->id, 'total_cost' => 7.5]);
        HealthRecord::factory()->create(['animal_id' => $animal->id, 'cost' => 5]);
        Expense::factory()->create(['batch_id' => $batch->id, 'amount' => 40]);
        SalesOrderItem::factory()->create(['animal_id' => $animal->id, 'line_total' => 1400]);

        // 1400 revenue - 500 purchase - 7.5 feed - 5 health - 40 other = 847.5
        $this->assertSame(847.5, $batch->netProfit());
    }

    public function test_feed_conversion_ratio_is_feed_kg_over_weight_gain(): void
    {
        $batch = Batch::factory()->create();
        $animal = Animal::factory()->create(['batch_id' => $batch->id, 'entry_weight_kg' => 250]);
        $animal->weighIns()->create(['weigh_date' => now(), 'weight_kg' => 350]);
        FeedLog::factory()->create(['batch_id' => $batch->id, 'quantity_kg' => 200, 'total_cost' => 100]);

        // 100kg gain, 200kg feed -> FCR 2.0
        $this->assertSame(2.0, $batch->feedConversionRatio());
    }

    public function test_feed_conversion_ratio_is_null_when_there_is_no_weight_gain(): void
    {
        $batch = Batch::factory()->create();
        Animal::factory()->create(['batch_id' => $batch->id, 'entry_weight_kg' => 250]);

        $this->assertNull($batch->feedConversionRatio());
    }

    public function test_accrual_breakdown_treats_unsold_animals_as_wip_not_loss(): void
    {
        $batch = Batch::factory()->create();
        Animal::factory()->create(['batch_id' => $batch->id, 'purchase_price' => 100, 'status' => 'on_feed']);
        Animal::factory()->create(['batch_id' => $batch->id, 'purchase_price' => 100, 'status' => 'on_feed']);
        FeedLog::factory()->create(['batch_id' => $batch->id, 'total_cost' => 40]);

        $breakdown = $batch->accrualBreakdown();

        $this->assertSame(0.0, $breakdown['realized_revenue']);
        $this->assertSame(0.0, $breakdown['realized_cogs']);
        $this->assertSame(0.0, $breakdown['mortality_loss']);
        $this->assertSame(240.0, $breakdown['wip_value']); // (100 + 20 shared feed) * 2
        $this->assertSame(0.0, $breakdown['net_profit']);
    }

    public function test_accrual_breakdown_splits_sold_dead_and_on_feed_animals(): void
    {
        $batch = Batch::factory()->create();
        $sold1 = Animal::factory()->create(['batch_id' => $batch->id, 'purchase_price' => 100, 'status' => 'sold']);
        $sold2 = Animal::factory()->create(['batch_id' => $batch->id, 'purchase_price' => 100, 'status' => 'sold']);
        Animal::factory()->create(['batch_id' => $batch->id, 'purchase_price' => 100, 'status' => 'dead']);
        Animal::factory()->create(['batch_id' => $batch->id, 'purchase_price' => 100, 'status' => 'on_feed']);
        FeedLog::factory()->create(['batch_id' => $batch->id, 'total_cost' => 40]);
        HealthRecord::factory()->create(['animal_id' => $sold1->id, 'cost' => 5]);
        SalesOrderItem::factory()->create(['animal_id' => $sold1->id, 'line_total' => 200]);
        SalesOrderItem::factory()->create(['animal_id' => $sold2->id, 'line_total' => 150]);

        $breakdown = $batch->accrualBreakdown();

        // Shared feed+other cost is 40 / 4 head = 10 each.
        $this->assertSame(350.0, $breakdown['realized_revenue']);
        $this->assertSame(225.0, $breakdown['realized_cogs']); // (100+5+10) + (100+0+10)
        $this->assertSame(110.0, $breakdown['mortality_loss']); // 100+0+10
        $this->assertSame(110.0, $breakdown['wip_value']); // 100+0+10
        $this->assertSame(15.0, $breakdown['net_profit']); // 350 - 225 - 110
    }
}
