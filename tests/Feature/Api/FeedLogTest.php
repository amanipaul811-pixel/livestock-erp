<?php

namespace Tests\Feature\Api;

use App\Models\Batch;
use App\Models\FeedItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class FeedLogTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_feeder_can_log_feed_and_cost_is_computed(): void
    {
        Sanctum::actingAs($this->userWithRole('Feeder'));
        $batch = Batch::factory()->create();
        $feedItem = FeedItem::factory()->create(['cost_per_unit' => 0.5]);

        $response = $this->postJson("/api/batches/{$batch->id}/feed-logs", [
            'feed_item_id' => $feedItem->id,
            'feed_date' => now()->toDateString(),
            'quantity_kg' => 10,
        ]);

        $response->assertCreated()->assertJsonPath('total_cost', 5);
        $this->assertDatabaseHas('feed_stock_movements', [
            'feed_item_id' => $feedItem->id,
            'type' => 'out',
            'quantity_kg' => 10,
        ]);
    }

    public function test_invalid_batch_id_on_feed_log_returns_404(): void
    {
        Sanctum::actingAs($this->userWithRole('Feeder'));
        $feedItem = FeedItem::factory()->create();

        $response = $this->postJson('/api/batches/99999/feed-logs', [
            'feed_item_id' => $feedItem->id,
            'feed_date' => now()->toDateString(),
            'quantity_kg' => 10,
        ]);

        $response->assertNotFound();
    }

    public function test_sales_role_cannot_log_feed(): void
    {
        Sanctum::actingAs($this->userWithRole('Sales'));
        $batch = Batch::factory()->create();
        $feedItem = FeedItem::factory()->create();

        $response = $this->postJson("/api/batches/{$batch->id}/feed-logs", [
            'feed_item_id' => $feedItem->id,
            'feed_date' => now()->toDateString(),
            'quantity_kg' => 10,
        ]);

        $response->assertForbidden();
    }
}
