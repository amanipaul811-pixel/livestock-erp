<?php

namespace Tests\Feature\Web;

use App\Models\Batch;
use App\Models\PurchaseOrder;
use App\Models\Species;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class PurchaseOrderAnimalReceiptTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_receiving_an_animal_order_creates_its_own_dedicated_batch_but_no_animals(): void
    {
        $admin = $this->adminUser();
        $species = Species::factory()->create();
        $order = PurchaseOrder::factory()->create([
            'order_type' => 'animal',
            'species_id' => $species->id,
            'quantity' => 3,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->patch(route('purchase-orders.update-status', $order), ['status' => 'received'])
            ->assertRedirect();

        $order->refresh();
        $this->assertSame(0, $order->animalsReceivedCount());
        $this->assertNotNull($order->batch_id);
        $this->assertSame($species->id, $order->batch->species_id);
        $this->assertSame('active', $order->batch->status);
    }

    public function test_two_animal_orders_never_share_a_batch_even_for_the_same_species_and_supplier(): void
    {
        $admin = $this->adminUser();
        $species = Species::factory()->create();
        $supplier = Supplier::factory()->create();
        $first = PurchaseOrder::factory()->create([
            'order_type' => 'animal', 'species_id' => $species->id, 'supplier_id' => $supplier->id,
            'quantity' => 5, 'status' => 'pending',
        ]);
        $second = PurchaseOrder::factory()->create([
            'order_type' => 'animal', 'species_id' => $species->id, 'supplier_id' => $supplier->id,
            'quantity' => 5, 'status' => 'pending',
        ]);

        $this->actingAs($admin)->patch(route('purchase-orders.update-status', $first), ['status' => 'received']);
        $this->actingAs($admin)->patch(route('purchase-orders.update-status', $second), ['status' => 'received']);

        $first->refresh();
        $second->refresh();
        $this->assertNotNull($first->batch_id);
        $this->assertNotNull($second->batch_id);
        $this->assertNotSame($first->batch_id, $second->batch_id);
    }

    public function test_receiving_the_same_order_twice_does_not_create_a_second_batch(): void
    {
        $admin = $this->adminUser();
        $species = Species::factory()->create();
        $order = PurchaseOrder::factory()->create([
            'order_type' => 'animal', 'species_id' => $species->id, 'quantity' => 2, 'status' => 'pending',
        ]);

        $this->actingAs($admin)->patch(route('purchase-orders.update-status', $order), ['status' => 'received']);
        $firstBatchId = $order->fresh()->batch_id;

        // Visiting the order again (the self-healing backfill check) must not
        // create a second batch now that one already exists.
        $this->actingAs($admin)->get(route('purchase-orders.show', $order));

        $this->assertSame($firstBatchId, $order->fresh()->batch_id);
        $this->assertSame(1, Batch::where('species_id', $species->id)->count());
    }

    public function test_recording_an_animal_against_the_purchase_order_links_it_and_returns_to_the_order(): void
    {
        $admin = $this->adminUser();
        $species = Species::factory()->create();
        $order = PurchaseOrder::factory()->create([
            'order_type' => 'animal', 'species_id' => $species->id, 'quantity' => 2, 'status' => 'pending',
        ]);
        $this->actingAs($admin)->patch(route('purchase-orders.update-status', $order), ['status' => 'received']);
        $batch = $order->fresh()->batch;

        $response = $this->actingAs($admin)->get(route('animals.create', $batch).'?purchase_order_id='.$order->id);
        $response->assertOk();
        $response->assertSee($order->po_number);

        $response = $this->actingAs($admin)->post(route('animals.store', $batch), [
            'sex' => 'male',
            'entry_date' => now()->format('Y-m-d'),
            'entry_weight_kg' => 250,
            'purchase_price' => 500,
            'purchase_order_id' => $order->id,
        ]);

        $response->assertRedirect(route('purchase-orders.show', $order));
        $animal = $batch->animals()->firstOrFail();
        $this->assertSame($order->id, $animal->purchase_order_id);
        $this->assertMatchesRegularExpression('/^[A-Z]{1,3}-\d{6}$/', $animal->tag_id);
        $this->assertSame(1, $order->fresh()->animalsReceivedCount());
        $this->assertSame(1, $order->fresh()->animalsRemaining());
    }

    public function test_the_intake_form_preselects_the_purchase_orders_supplier(): void
    {
        $admin = $this->adminUser();
        $species = Species::factory()->create();
        $supplier = Supplier::factory()->create();
        $order = PurchaseOrder::factory()->create([
            'order_type' => 'animal', 'species_id' => $species->id, 'supplier_id' => $supplier->id,
            'quantity' => 1, 'status' => 'pending',
        ]);
        $this->actingAs($admin)->patch(route('purchase-orders.update-status', $order), ['status' => 'received']);
        $batch = $order->fresh()->batch;

        $response = $this->actingAs($admin)->get(route('animals.create', $batch).'?purchase_order_id='.$order->id);

        $response->assertOk();
        $response->assertSee('value="'.$supplier->id.'" selected', false);
    }

    public function test_a_purchase_order_id_for_a_mismatched_species_is_ignored(): void
    {
        $admin = $this->adminUser();
        $cattle = Species::factory()->create();
        $goats = Species::factory()->create();
        $order = PurchaseOrder::factory()->create([
            'order_type' => 'animal', 'species_id' => $cattle->id, 'quantity' => 1, 'status' => 'received',
        ]);
        $goatBatch = Batch::factory()->create(['species_id' => $goats->id, 'status' => 'active']);

        $response = $this->actingAs($admin)->post(route('animals.store', $goatBatch), [
            'sex' => 'female',
            'entry_date' => now()->format('Y-m-d'),
            'entry_weight_kg' => 30,
            'purchase_price' => 80,
            'purchase_order_id' => $order->id,
        ]);

        $animal = $goatBatch->animals()->firstOrFail();
        $response->assertRedirect(route('animals.show', $animal));
        $this->assertNull($animal->purchase_order_id);
    }

    public function test_creating_an_animal_order_requires_species_and_head_count(): void
    {
        $admin = $this->adminUser();
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($admin)->post(route('purchase-orders.store'), [
            'supplier_id' => $supplier->id,
            'order_type' => 'animal',
            'order_date' => now()->format('Y-m-d'),
            'total_amount' => 1000,
        ]);

        $response->assertSessionHasErrors(['species_id', 'quantity']);
    }
}
