<?php

namespace Tests\Feature\Web;

use App\Models\Animal;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class SupplierCrudTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_a_supplier_can_be_updated(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Original Name']);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))->put("/suppliers/{$supplier->id}", [
            'name' => 'Updated Name',
            'supplier_type' => 'feed',
        ]);

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'name' => 'Updated Name']);
    }

    public function test_a_supplier_with_no_dependents_can_be_deleted(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->userWithRole('Farm Manager'))->delete("/suppliers/{$supplier->id}");

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    public function test_a_supplier_with_animals_cannot_be_deleted(): void
    {
        $supplier = Supplier::factory()->create();
        Animal::factory()->create(['supplier_id' => $supplier->id]);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))->delete("/suppliers/{$supplier->id}");

        $response->assertSessionHasErrors('supplier');
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id]);
    }

    public function test_a_feeder_cannot_edit_a_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->userWithRole('Feeder'))->put("/suppliers/{$supplier->id}", [
            'name' => 'Hacked Name',
            'supplier_type' => 'feed',
        ]);

        $response->assertForbidden();
    }
}
