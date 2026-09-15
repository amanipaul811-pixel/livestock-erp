<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class SupplierWarehouseTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_farm_manager_can_create_a_supplier(): void
    {
        Sanctum::actingAs($this->userWithRole('Farm Manager'));

        $response = $this->postJson('/api/suppliers', [
            'name' => 'Rift Valley Ranchers',
            'supplier_type' => 'animal',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('suppliers', ['name' => 'Rift Valley Ranchers']);
    }

    public function test_farm_manager_can_create_a_warehouse(): void
    {
        Sanctum::actingAs($this->userWithRole('Farm Manager'));

        $response = $this->postJson('/api/warehouses', [
            'name' => 'Main Store',
            'type' => 'feed',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('warehouses', ['name' => 'Main Store']);
    }

    public function test_feeder_cannot_create_a_supplier(): void
    {
        Sanctum::actingAs($this->userWithRole('Feeder'));

        $response = $this->postJson('/api/suppliers', [
            'name' => 'Should Fail Co',
            'supplier_type' => 'animal',
        ]);

        $response->assertForbidden();
    }
}
