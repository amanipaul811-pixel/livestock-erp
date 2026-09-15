<?php

namespace Tests\Feature\Api;

use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_a_partial_payment_reduces_the_sales_order_balance(): void
    {
        Sanctum::actingAs($this->adminUser());
        $order = SalesOrder::factory()->create(['total_amount' => 1200]);

        $response = $this->postJson("/api/sales-orders/{$order->id}/payments", [
            'payment_date' => now()->toDateString(),
            'amount' => 700,
            'method' => 'cash',
        ]);

        $response->assertCreated();
        $this->assertEquals(500, $order->fresh()->balanceDue());
    }

    public function test_a_sales_order_cannot_be_overpaid(): void
    {
        Sanctum::actingAs($this->adminUser());
        $order = SalesOrder::factory()->create(['total_amount' => 1200]);

        $response = $this->postJson("/api/sales-orders/{$order->id}/payments", [
            'payment_date' => now()->toDateString(),
            'amount' => 9999,
            'method' => 'cash',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('amount');
    }

    public function test_a_purchase_order_cannot_be_overpaid(): void
    {
        Sanctum::actingAs($this->adminUser());
        $order = PurchaseOrder::factory()->create(['total_amount' => 500]);

        $response = $this->postJson("/api/purchase-orders/{$order->id}/payments", [
            'payment_date' => now()->toDateString(),
            'amount' => 501,
            'method' => 'cash',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('amount');
    }

    public function test_purchase_order_payment_reduces_its_balance(): void
    {
        Sanctum::actingAs($this->adminUser());
        $order = PurchaseOrder::factory()->create(['total_amount' => 500]);

        $this->postJson("/api/purchase-orders/{$order->id}/payments", [
            'payment_date' => now()->toDateString(),
            'amount' => 200,
            'method' => 'bank_transfer',
        ])->assertCreated();

        $this->assertEquals(300, $order->fresh()->balanceDue());
    }
}
