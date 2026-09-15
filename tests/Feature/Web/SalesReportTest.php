<?php

namespace Tests\Feature\Web;

use App\Models\Animal;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class SalesReportTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_it_lists_sales_in_range_and_totals_revenue(): void
    {
        $customer = Customer::factory()->create(['name' => 'Acme Meats']);
        $animal = Animal::factory()->create();
        $so = SalesOrder::factory()->create(['customer_id' => $customer->id, 'sale_date' => now(), 'total_amount' => 1400]);
        SalesOrderItem::factory()->create(['sales_order_id' => $so->id, 'animal_id' => $animal->id, 'line_total' => 1400]);

        $response = $this->actingAs($this->userWithRole('Sales'))->get('/reports/sales');

        $response->assertOk();
        $response->assertSee('Acme Meats');
        $response->assertViewHas('totalRevenue', 1400.0);
    }

    public function test_filtering_by_customer_excludes_other_customers(): void
    {
        $match = Customer::factory()->create();
        $other = Customer::factory()->create();
        $animal1 = Animal::factory()->create();
        $animal2 = Animal::factory()->create();
        $so1 = SalesOrder::factory()->create(['customer_id' => $match->id, 'sale_date' => now()]);
        $so2 = SalesOrder::factory()->create(['customer_id' => $other->id, 'sale_date' => now()]);
        SalesOrderItem::factory()->create(['sales_order_id' => $so1->id, 'animal_id' => $animal1->id, 'line_total' => 500]);
        SalesOrderItem::factory()->create(['sales_order_id' => $so2->id, 'animal_id' => $animal2->id, 'line_total' => 700]);

        $response = $this->actingAs($this->userWithRole('Sales'))->get("/reports/sales?customer_id={$match->id}");

        $response->assertOk();
        $response->assertViewHas('totalRevenue', 500.0);
    }

    public function test_a_feeder_cannot_view_the_sales_report(): void
    {
        $response = $this->actingAs($this->userWithRole('Feeder'))->get('/reports/sales');

        $response->assertForbidden();
    }

    public function test_pdf_export_downloads_a_pdf(): void
    {
        $response = $this->actingAs($this->userWithRole('Sales'))->get('/reports/sales/export/pdf');

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }
}
