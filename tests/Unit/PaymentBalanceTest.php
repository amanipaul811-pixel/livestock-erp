<?php

namespace Tests\Unit;

use App\Models\Payment;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentBalanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_order_balance_due_is_total_minus_payments(): void
    {
        $order = SalesOrder::factory()->create(['total_amount' => 1200]);
        Payment::factory()->create(['reference_type' => 'sales_order', 'reference_id' => $order->id, 'amount' => 700]);

        $this->assertSame(700.0, $order->amountPaid());
        $this->assertSame(500.0, $order->balanceDue());
    }

    public function test_sales_order_with_no_payments_owes_the_full_amount(): void
    {
        $order = SalesOrder::factory()->create(['total_amount' => 1200]);

        $this->assertSame(0.0, $order->amountPaid());
        $this->assertSame(1200.0, $order->balanceDue());
    }

    public function test_purchase_order_balance_due_is_total_minus_payments(): void
    {
        $order = PurchaseOrder::factory()->create(['total_amount' => 500]);
        Payment::factory()->create(['reference_type' => 'purchase_order', 'reference_id' => $order->id, 'amount' => 200]);

        $this->assertSame(200.0, $order->amountPaid());
        $this->assertSame(300.0, $order->balanceDue());
    }

    public function test_payments_on_a_different_reference_type_are_not_counted(): void
    {
        $salesOrder = SalesOrder::factory()->create(['total_amount' => 1000]);
        $purchaseOrder = PurchaseOrder::factory()->create(['total_amount' => 1000]);
        // Same reference_id by coincidence, different reference_type
        Payment::factory()->create(['reference_type' => 'purchase_order', 'reference_id' => $salesOrder->id, 'amount' => 999]);

        $this->assertSame(0.0, $salesOrder->amountPaid());
    }
}
