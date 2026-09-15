<?php

namespace Tests\Feature\Web;

use App\Models\FeedItem;
use App\Models\FeedStockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class StockReportTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_it_totals_in_and_out_movements(): void
    {
        $feedItem = FeedItem::factory()->create();
        FeedStockMovement::factory()->create(['feed_item_id' => $feedItem->id, 'type' => 'in', 'quantity_kg' => 200, 'occurred_at' => now()]);
        FeedStockMovement::factory()->create(['feed_item_id' => $feedItem->id, 'type' => 'out', 'quantity_kg' => 50, 'occurred_at' => now()]);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get('/reports/stock');

        $response->assertOk();
        $response->assertViewHas('totalIn', 200.0);
        $response->assertViewHas('totalOut', 50.0);
    }

    public function test_filtering_to_a_single_feed_item_shows_a_running_balance(): void
    {
        $feedItem = FeedItem::factory()->create();
        FeedStockMovement::factory()->create(['feed_item_id' => $feedItem->id, 'type' => 'in', 'quantity_kg' => 100, 'occurred_at' => now()->subDays(5)]);
        FeedStockMovement::factory()->create(['feed_item_id' => $feedItem->id, 'type' => 'out', 'quantity_kg' => 30, 'occurred_at' => now()->subDays(1)]);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))
            ->get("/reports/stock?feed_item_id={$feedItem->id}&from=".now()->subDays(10)->toDateString());

        $response->assertOk();
        $response->assertViewHas('runningBalance', 70.0);
        $response->assertViewHas('currentStock', 70.0);
    }

    public function test_a_vet_cannot_view_the_stock_report(): void
    {
        $response = $this->actingAs($this->userWithRole('Vet'))->get('/reports/stock');

        $response->assertForbidden();
    }

    public function test_pdf_export_downloads_a_pdf(): void
    {
        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get('/reports/stock/export/pdf');

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }
}
