<?php

namespace Tests\Feature\Web;

use App\Models\FeedItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class FeedItemRestockTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_a_logged_in_user_can_restock_a_feed_item(): void
    {
        $feedItem = FeedItem::factory()->create();

        $response = $this->actingAs($this->adminUser())
            ->post("/feed-items/{$feedItem->id}/restock", [
                'quantity_kg' => 200,
                'reason' => 'Supplier delivery',
            ]);

        $response->assertRedirect(route('feed-items.index'));
        $this->assertSame(200.0, $feedItem->fresh()->currentStock());
    }

    public function test_restock_quantity_is_required(): void
    {
        $feedItem = FeedItem::factory()->create();

        $response = $this->actingAs($this->adminUser())
            ->post("/feed-items/{$feedItem->id}/restock", []);

        $response->assertSessionHasErrors('quantity_kg');
    }
}
