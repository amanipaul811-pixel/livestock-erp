<?php

namespace Tests\Feature\Api;

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
            'order_type' => 'feed',
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
