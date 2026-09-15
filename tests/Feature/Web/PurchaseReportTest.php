<?php

namespace Tests\Feature\Web;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class PurchaseReportTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_it_lists_purchase_orders_in_range_and_totals_them(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Grain Traders']);
        PurchaseOrder::factory()->create(['supplier_id' => $supplier->id, 'order_date' => now(), 'total_amount' => 900]);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get('/reports/purchases');

        $response->assertOk();
        $response->assertSee('Grain Traders');
        $response->assertViewHas('totalOrdered', 900.0);
    }

    public function test_filtering_by_order_type_excludes_other_types(): void
    {
        $supplier = Supplier::factory()->create();
        PurchaseOrder::factory()->create(['supplier_id' => $supplier->id, 'order_type' => 'feed', 'order_date' => now(), 'total_amount' => 400]);
        PurchaseOrder::factory()->create(['supplier_id' => $supplier->id, 'order_type' => 'medicine', 'order_date' => now(), 'total_amount' => 100]);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get('/reports/purchases?order_type=feed');

        $response->assertOk();
        $response->assertViewHas('totalOrdered', 400.0);
    }

    public function test_a_vet_cannot_view_the_purchases_report(): void
    {
        $response = $this->actingAs($this->userWithRole('Vet'))->get('/reports/purchases');

        $response->assertForbidden();
    }

    public function test_pdf_export_downloads_a_pdf(): void
    {
        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get('/reports/purchases/export/pdf');

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }
}
