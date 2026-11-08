<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\FeedItem;
use App\Models\Pen;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Species;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class ClearSampleDataTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    private function seedSampleData(): void
    {
        $this->adminUser();
        Pen::factory()->create();
        $species = Species::factory()->create();
        $batch = Batch::factory()->create(['species_id' => $species->id]);
        $animal = Animal::factory()->create(['batch_id' => $batch->id, 'species_id' => $species->id]);
        $order = SalesOrder::factory()->create();
        SalesOrderItem::factory()->create(['sales_order_id' => $order->id, 'animal_id' => $animal->id]);
        PurchaseOrder::factory()->create();
        FeedItem::factory()->create();
        Supplier::factory()->create();
        Customer::factory()->create();
    }

    public function test_it_deletes_records_and_master_data_but_keeps_logins_species_and_pens(): void
    {
        $this->seedSampleData();

        $this->artisan('app:clear-sample-data', ['--force' => true])->assertSuccessful();

        foreach ([Animal::class, Batch::class, SalesOrder::class, SalesOrderItem::class, PurchaseOrder::class, FeedItem::class, Supplier::class, Customer::class] as $model) {
            $this->assertSame(0, $model::count(), $model.' should be empty');
        }
        $this->assertGreaterThan(0, User::count());
        $this->assertGreaterThan(0, Species::count());
        $this->assertGreaterThan(0, Pen::count());
    }

    public function test_keep_master_data_leaves_suppliers_customers_and_feed_items(): void
    {
        $this->seedSampleData();

        $this->artisan('app:clear-sample-data', ['--force' => true, '--keep-master-data' => true])->assertSuccessful();

        $this->assertSame(0, Animal::count());
        $this->assertSame(0, PurchaseOrder::count());
        $this->assertGreaterThan(0, Supplier::count());
        $this->assertGreaterThan(0, Customer::count());
        $this->assertGreaterThan(0, FeedItem::count());
    }

    public function test_it_deletes_nothing_unless_delete_is_typed(): void
    {
        $this->seedSampleData();

        $this->artisan('app:clear-sample-data')->expectsQuestion('Type DELETE to confirm', 'no')->assertFailed();

        $this->assertGreaterThan(0, Animal::count());
    }

    public function test_numbering_restarts_from_one_after_clearing(): void
    {
        $this->seedSampleData();
        $this->artisan('app:clear-sample-data', ['--force' => true])->assertSuccessful();

        $this->assertSame('PO-'.now()->year.'-0001', PurchaseOrder::nextPoNumber());
        $this->assertSame('B-'.now()->year.'-0001', Batch::nextBatchCode());
    }
}
