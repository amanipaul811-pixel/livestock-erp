<?php

namespace Tests\Feature\Web;

use App\Models\Animal;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\FeedItem;
use App\Models\FeedStockMovement;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_the_report_totals_revenue_and_profit_for_sold_animals_in_range(): void
    {
        $batch = Batch::factory()->create(['start_date' => now()->subDays(10)]);
        $animal = Animal::factory()->create(['batch_id' => $batch->id, 'purchase_price' => 500, 'status' => 'sold']);
        $customer = Customer::factory()->create();
        $so = SalesOrder::factory()->create(['customer_id' => $customer->id, 'sale_date' => now(), 'total_amount' => 1400]);
        SalesOrderItem::factory()->create(['sales_order_id' => $so->id, 'animal_id' => $animal->id, 'line_total' => 1400]);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get('/reports');

        $response->assertOk();
        $response->assertViewHas('totals', fn (array $totals) => $totals['net_profit'] === 900.0); // 1400 revenue - 500 purchase cost
    }

    public function test_an_active_batch_with_no_sales_shows_zero_profit_not_a_loss(): void
    {
        $batch = Batch::factory()->create(['start_date' => now()->subDays(10), 'status' => 'active']);
        Animal::factory()->create(['batch_id' => $batch->id, 'purchase_price' => 500, 'status' => 'on_feed']);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get('/reports');

        $response->assertOk();
        // The 500 spent on the still-unsold animal shows up as WIP value, not
        // a loss -- this is exactly the gap the accrual switch closes.
        $response->assertViewHas('totals', fn (array $totals) => $totals['net_profit'] === 0.0);
        $response->assertViewHas('wipValue', 500.0);
    }

    public function test_accounts_receivable_aging_lists_an_unpaid_sale(): void
    {
        $customer = Customer::factory()->create(['name' => 'Slow Payer Ltd']);
        SalesOrder::factory()->create(['customer_id' => $customer->id, 'sale_date' => now()->subDays(45), 'total_amount' => 1000]);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get('/reports');

        $response->assertOk();
        $response->assertSee('Slow Payer Ltd');
        $response->assertSee('31-60 days');
    }

    public function test_accounts_payable_aging_lists_an_unpaid_purchase_order(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Overdue Supplier Co']);
        PurchaseOrder::factory()->create(['supplier_id' => $supplier->id, 'order_date' => now()->subDays(100), 'total_amount' => 800, 'status' => 'received']);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get('/reports');

        $response->assertOk();
        $response->assertSee('Overdue Supplier Co');
        $response->assertSee('90+ days');
    }

    public function test_feed_inventory_value_reflects_current_stock(): void
    {
        $feedItem = FeedItem::factory()->create(['cost_per_unit' => 2]);
        FeedStockMovement::factory()->create(['feed_item_id' => $feedItem->id, 'type' => 'in', 'quantity_kg' => 100]);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get('/reports');

        $response->assertOk();
        $response->assertViewHas('feedInventoryValue', 200.0); // 100kg * 2/kg
    }

    public function test_a_batch_outside_the_date_range_is_excluded(): void
    {
        Batch::factory()->create(['start_date' => now()->subYears(2)]);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))
            ->get('/reports?from='.now()->subMonth()->toDateString().'&to='.now()->toDateString());

        $response->assertOk();
        $response->assertSee('No batches started in this period.');
    }

    public function test_a_feeder_cannot_view_reports(): void
    {
        $response = $this->actingAs($this->userWithRole('Feeder'))->get('/reports');

        $response->assertForbidden();
    }

    public function test_pdf_export_downloads_a_pdf(): void
    {
        Batch::factory()->create(['start_date' => now()]);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get('/reports/export/pdf');

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_excel_export_downloads_a_spreadsheet(): void
    {
        Batch::factory()->create(['start_date' => now()]);
        Expense::factory()->create(['batch_id' => null, 'expense_date' => now(), 'amount' => 25]);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get('/reports/export/excel');

        $response->assertOk();
    }
}
