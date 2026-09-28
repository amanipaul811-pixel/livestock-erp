<?php

namespace Tests\Feature\Web;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class SalesOrderInvoiceTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_it_downloads_a_pdf_invoice(): void
    {
        $customer = Customer::factory()->create(['name' => 'Coastal Butchery Ltd']);
        $order = SalesOrder::factory()->create(['customer_id' => $customer->id, 'total_amount' => 1400]);
        SalesOrderItem::factory()->create(['sales_order_id' => $order->id]);
        Payment::factory()->create(['reference_id' => $order->id, 'amount' => 500]);

        $response = $this->actingAs($this->adminUser())->get(route('sales-orders.invoice', $order));

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_the_sales_order_page_links_to_the_invoice(): void
    {
        $order = SalesOrder::factory()->create();

        $response = $this->actingAs($this->adminUser())->get(route('sales-orders.show', $order));

        $response->assertOk();
        $response->assertSee(route('sales-orders.invoice', $order), false);
    }
}
