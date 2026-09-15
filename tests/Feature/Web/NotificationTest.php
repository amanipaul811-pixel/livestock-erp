<?php

namespace Tests\Feature\Web;

use App\Models\Animal;
use App\Models\Batch;
use App\Models\FeedItem;
use App\Models\FeedStockMovement;
use App\Notifications\AnimalReadyToSellNotification;
use App\Notifications\LowFeedStockNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_logging_feed_that_crosses_the_reorder_level_notifies_feeders(): void
    {
        Notification::fake();

        $feeder = $this->userWithRole('Feeder');
        $batch = Batch::factory()->create();
        $feedItem = FeedItem::factory()->create(['reorder_level' => 50]);
        FeedStockMovement::factory()->create(['feed_item_id' => $feedItem->id, 'type' => 'in', 'quantity_kg' => 55]);

        $this->actingAs($feeder)->post("/batches/{$batch->id}/feed-logs", [
            'feed_item_id' => $feedItem->id,
            'feed_date' => now()->toDateString(),
            'quantity_kg' => 10,
        ]);

        Notification::assertSentTo($feeder, LowFeedStockNotification::class);
    }

    public function test_logging_feed_that_stays_above_the_reorder_level_does_not_notify(): void
    {
        Notification::fake();

        $feeder = $this->userWithRole('Feeder');
        $batch = Batch::factory()->create();
        $feedItem = FeedItem::factory()->create(['reorder_level' => 0]);

        $this->actingAs($feeder)->post("/batches/{$batch->id}/feed-logs", [
            'feed_item_id' => $feedItem->id,
            'feed_date' => now()->toDateString(),
            'quantity_kg' => 10,
        ]);

        Notification::assertNothingSent();
    }

    public function test_a_weigh_in_that_reaches_target_weight_notifies_sales(): void
    {
        Notification::fake();

        $sales = $this->userWithRole('Sales');
        $animal = Animal::factory()->create(['entry_weight_kg' => 250]);
        $animal->species()->update(['target_exit_weight_kg' => 400]);

        $this->actingAs($this->adminUser())->post("/animals/{$animal->id}/weigh-ins", [
            'weigh_date' => now()->toDateString(),
            'weight_kg' => 400,
        ]);

        Notification::assertSentTo($sales, AnimalReadyToSellNotification::class);
    }

    public function test_a_weigh_in_that_does_not_reach_target_weight_does_not_notify(): void
    {
        Notification::fake();

        $this->userWithRole('Sales');
        $animal = Animal::factory()->create(['entry_weight_kg' => 250]);
        $animal->species()->update(['target_exit_weight_kg' => 400]);

        $this->actingAs($this->adminUser())->post("/animals/{$animal->id}/weigh-ins", [
            'weigh_date' => now()->toDateString(),
            'weight_kg' => 300,
        ]);

        Notification::assertNothingSent();
    }

    public function test_a_user_can_open_a_notification_and_it_is_marked_read(): void
    {
        $user = $this->adminUser();
        $animal = Animal::factory()->create();
        $user->notify(new AnimalReadyToSellNotification($animal));
        $notification = $user->notifications()->first();

        $response = $this->actingAs($user)->get("/notifications/{$notification->id}/open");

        $response->assertRedirect(route('animals.show', $animal));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_a_user_cannot_open_another_users_notification(): void
    {
        $owner = $this->adminUser();
        $intruder = $this->adminUser();
        $animal = Animal::factory()->create();
        $owner->notify(new AnimalReadyToSellNotification($animal));
        $notification = $owner->notifications()->first();

        $response = $this->actingAs($intruder)->get("/notifications/{$notification->id}/open");

        $response->assertRedirect(route('dashboard'));
        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_opening_a_stale_or_missing_notification_link_redirects_gracefully(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->get('/notifications/does-not-exist/open');

        $response->assertRedirect(route('dashboard'));
    }

    public function test_mark_all_read_clears_unread_notifications(): void
    {
        $user = $this->adminUser();
        $animal = Animal::factory()->create();
        $user->notify(new AnimalReadyToSellNotification($animal));

        $this->assertSame(1, $user->unreadNotifications()->count());

        $this->actingAs($user)->post('/notifications/mark-all-read');

        $this->assertSame(0, $user->unreadNotifications()->count());
    }
}
