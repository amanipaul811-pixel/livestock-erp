<?php

namespace Tests\Unit;

use App\Models\Animal;
use App\Models\Batch;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\Species;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SequentialCodeGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_po_numbers_increment_within_the_current_year(): void
    {
        $this->assertSame('PO-'.now()->year.'-0001', PurchaseOrder::nextPoNumber());

        PurchaseOrder::factory()->create(['po_number' => PurchaseOrder::nextPoNumber()]);

        $this->assertSame('PO-'.now()->year.'-0002', PurchaseOrder::nextPoNumber());
    }

    public function test_so_numbers_increment_within_the_current_year(): void
    {
        $this->assertSame('SO-'.now()->year.'-0001', SalesOrder::nextSoNumber());

        SalesOrder::factory()->create(['so_number' => SalesOrder::nextSoNumber()]);

        $this->assertSame('SO-'.now()->year.'-0002', SalesOrder::nextSoNumber());
    }

    public function test_batch_codes_increment_within_the_current_year(): void
    {
        $this->assertSame('B-'.now()->year.'-0001', Batch::nextBatchCode());

        Batch::factory()->create(['batch_code' => Batch::nextBatchCode()]);

        $this->assertSame('B-'.now()->year.'-0002', Batch::nextBatchCode());
    }

    public function test_tag_ids_are_prefixed_by_species_and_increment_independently_per_species(): void
    {
        $cattle = Species::factory()->create(['name' => 'Cattle']);
        $goat = Species::factory()->create(['name' => 'Goat']);

        $this->assertSame('CAT-000001', Animal::nextTagId($cattle));
        $this->assertSame('GOA-000001', Animal::nextTagId($goat));

        Animal::factory()->create(['species_id' => $cattle->id, 'tag_id' => Animal::nextTagId($cattle)]);

        $this->assertSame('CAT-000002', Animal::nextTagId($cattle));
        $this->assertSame('GOA-000001', Animal::nextTagId($goat));
    }
}
