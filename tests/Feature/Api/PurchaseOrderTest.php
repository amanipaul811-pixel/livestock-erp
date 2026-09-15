<?php

namespace Tests\Feature\Api;

use App\Models\Expense;
use App\Models\FeedItem;
use App\Models\FeedStockMovement;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_farm_manager_can_create_a_purchase_order(): void
    {
        Sanctum::actingAs($this->userWithRole('Farm Manager'));
        $supplier = Supplier::factory()->create();

        $response = $this->postJson('/api/purchase-orders', [
            'supplier_id' => $supplier->id,
            'order_type' => 'animal',
            'order_date' => now()->toDateString(),
            'total_amount' => 2500,
        ]);

        $response->assertCreated()->assertJsonPath('status', 'pending');
    }

    public function test_it_can_be_marked_received(): void
    {
        Sanctum::actingAs($this->userWithRole('Farm Manager'));
        $order = PurchaseOrder::factory()->create(['status' => 'pending']);

        $response = $this->patchJson("/api/purchase-orders/{$order->id}", ['status' => 'received']);

        $response->assertOk()->assertJsonPath('status', 'received');
    }

    public function test_a_received_order_cannot_be_changed_back_to_pending(): void
    {
        Sanctum::actingAs($this->userWithRole('Farm Manager'));
        $order = PurchaseOrder::factory()->create(['status' => 'received']);

        $response = $this->patchJson("/api/purchase-orders/{$order->id}", ['status' => 'pending']);

        $response->assertStatus(422)->assertJsonValidationErrors('status');
        $this->assertSame('received', $order->fresh()->status);
    }

    public function test_a_cancelled_order_cannot_be_marked_received(): void
    {
        Sanctum::actingAs($this->userWithRole('Farm Manager'));
        $order = PurchaseOrder::factory()->create(['status' => 'cancelled']);

        $response = $this->patchJson("/api/purchase-orders/{$order->id}", ['status' => 'received']);

        $response->assertStatus(422)->assertJsonValidationErrors('status');
    }

    public function test_a_feed_purchase_order_requires_a_feed_item_and_quantity(): void
    {
        Sanctum::actingAs($this->userWithRole('Farm Manager'));
        $supplier = Supplier::factory()->create();

        $response = $this->postJson('/api/purchase-orders', [
            'supplier_id' => $supplier->id,
            'order_type' => 'feed',
            'order_date' => now()->toDateString(),
            'total_amount' => 2500,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['feed_item_id', 'quantity_kg']);
    }

    public function test_marking_a_feed_order_received_adds_stock(): void
    {
        Sanctum::actingAs($this->userWithRole('Farm Manager'));
        $feedItem = FeedItem::factory()->create();
        $order = PurchaseOrder::factory()->create([
            'status' => 'pending', 'order_type' => 'feed', 'feed_item_id' => $feedItem->id, 'quantity_kg' => 300,
        ]);

        $this->patchJson("/api/purchase-orders/{$order->id}", ['status' => 'received'])->assertOk();

        $this->assertSame(300.0, $feedItem->fresh()->currentStock());
        $this->assertDatabaseHas('feed_stock_movements', ['feed_item_id' => $feedItem->id, 'type' => 'in', 'quantity_kg' => 300]);
    }

    public function test_marking_a_medicine_order_received_creates_an_expense(): void
    {
        Sanctum::actingAs($this->userWithRole('Farm Manager'));
        $order = PurchaseOrder::factory()->create(['status' => 'pending', 'order_type' => 'medicine', 'total_amount' => 150]);

        $this->patchJson("/api/purchase-orders/{$order->id}", ['status' => 'received'])->assertOk();

        $this->assertDatabaseHas('expenses', ['batch_id' => null, 'category' => 'other', 'amount' => 150]);
    }

    public function test_marking_an_animal_order_received_creates_no_side_effect(): void
    {
        Sanctum::actingAs($this->userWithRole('Farm Manager'));
        $order = PurchaseOrder::factory()->create(['status' => 'pending', 'order_type' => 'animal', 'total_amount' => 2500]);

        $this->patchJson("/api/purchase-orders/{$order->id}", ['status' => 'received'])->assertOk();

        $this->assertSame(0, Expense::count());
        $this->assertSame(0, FeedStockMovement::count());
    }

    public function test_vet_cannot_create_a_purchase_order(): void
    {
        Sanctum::actingAs($this->userWithRole('Vet'));
        $supplier = Supplier::factory()->create();

        $response = $this->postJson('/api/purchase-orders', [
            'supplier_id' => $supplier->id,
            'order_type' => 'feed',
            'order_date' => now()->toDateString(),
            'total_amount' => 2500,
        ]);

        $response->assertForbidden();
    }
}
