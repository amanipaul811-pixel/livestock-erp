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

    public function test_receiving_an_animal_order_does_not_auto_create_any_animal(): void
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

        $this->assertSame(0, $order->fresh()->animalsReceivedCount());
    }

    public function test_recording_an_animal_against_the_purchase_order_links_it_and_returns_to_the_order(): void
    {
        $admin = $this->adminUser();
        $species = Species::factory()->create();
        $order = PurchaseOrder::factory()->create([
            'order_type' => 'animal',
            'species_id' => $species->id,
            'quantity' => 2,
            'status' => 'received',
        ]);
        $batch = Batch::factory()->create(['species_id' => $species->id, 'status' => 'active']);

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
            'order_type' => 'animal',
            'species_id' => $species->id,
            'supplier_id' => $supplier->id,
            'quantity' => 1,
            'status' => 'received',
        ]);
        $batch = Batch::factory()->create(['species_id' => $species->id, 'status' => 'active']);

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
            'order_type' => 'animal',
            'species_id' => $cattle->id,
            'quantity' => 1,
            'status' => 'received',
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
