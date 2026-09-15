<?php

namespace Tests\Feature\Web;

use App\Models\Animal;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_the_report_totals_revenue_and_profit_for_batches_in_range(): void
    {
        $batch = Batch::factory()->create(['start_date' => now()->subDays(10)]);
        $animal = Animal::factory()->create(['batch_id' => $batch->id, 'purchase_price' => 500]);
        $customer = Customer::factory()->create();
        $so = SalesOrder::factory()->create(['customer_id' => $customer->id, 'sale_date' => now(), 'total_amount' => 1400]);
        SalesOrderItem::factory()->create(['sales_order_id' => $so->id, 'animal_id' => $animal->id, 'line_total' => 1400]);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get('/reports');

        $response->assertOk();
        $response->assertSee(number_format(900.0, 2)); // 1400 revenue - 500 purchase cost
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
