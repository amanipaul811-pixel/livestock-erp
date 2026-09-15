<?php

namespace Tests\Feature\Api;

use App\Models\Animal;
use App\Models\Batch;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class SalesOrderTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_selling_the_last_animal_in_a_batch_closes_it(): void
    {
        Sanctum::actingAs($this->userWithRole('Sales'));
        $batch = Batch::factory()->create(['status' => 'active']);
        $animal = Animal::factory()->create(['batch_id' => $batch->id, 'status' => 'on_feed']);
        $customer = Customer::factory()->create();

        $response = $this->postJson('/api/sales-orders', [
            'customer_id' => $customer->id,
            'sale_date' => now()->toDateString(),
            'items' => [
                ['animal_id' => $animal->id, 'sale_weight_kg' => 400, 'price_per_kg' => 3.5],
            ],
        ]);

        $response->assertCreated()->assertJsonPath('total_amount', 1400);
        $this->assertDatabaseHas('animals', ['id' => $animal->id, 'status' => 'sold']);
        $this->assertDatabaseHas('batches', ['id' => $batch->id, 'status' => 'closed']);
    }

    public function test_selling_one_of_two_animals_partially_sells_the_batch(): void
    {
        Sanctum::actingAs($this->userWithRole('Sales'));
        $batch = Batch::factory()->create(['status' => 'active']);
        $sold = Animal::factory()->create(['batch_id' => $batch->id, 'status' => 'on_feed']);
        Animal::factory()->create(['batch_id' => $batch->id, 'status' => 'on_feed']);
        $customer = Customer::factory()->create();

        $this->postJson('/api/sales-orders', [
            'customer_id' => $customer->id,
            'sale_date' => now()->toDateString(),
            'items' => [
                ['animal_id' => $sold->id, 'sale_weight_kg' => 400, 'price_per_kg' => 3.5],
            ],
        ])->assertCreated();

        $this->assertDatabaseHas('batches', ['id' => $batch->id, 'status' => 'partially_sold']);
    }

    public function test_an_already_sold_animal_cannot_be_sold_again(): void
    {
        Sanctum::actingAs($this->userWithRole('Sales'));
        $animal = Animal::factory()->create(['status' => 'sold']);
        $customer = Customer::factory()->create();

        $response = $this->postJson('/api/sales-orders', [
            'customer_id' => $customer->id,
            'sale_date' => now()->toDateString(),
            'items' => [
                ['animal_id' => $animal->id, 'sale_weight_kg' => 400, 'price_per_kg' => 3.5],
            ],
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('items.0.animal_id');
    }

    public function test_the_same_animal_cannot_appear_twice_in_one_sales_order(): void
    {
        Sanctum::actingAs($this->userWithRole('Sales'));
        $animal = Animal::factory()->create(['status' => 'on_feed']);
        $customer = Customer::factory()->create();

        $response = $this->postJson('/api/sales-orders', [
            'customer_id' => $customer->id,
            'sale_date' => now()->toDateString(),
            'items' => [
                ['animal_id' => $animal->id, 'sale_weight_kg' => 400, 'price_per_kg' => 3.5],
                ['animal_id' => $animal->id, 'sale_weight_kg' => 400, 'price_per_kg' => 3.5],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_feeder_cannot_create_a_sales_order(): void
    {
        Sanctum::actingAs($this->userWithRole('Feeder'));
        $animal = Animal::factory()->create();
        $customer = Customer::factory()->create();

        $response = $this->postJson('/api/sales-orders', [
            'customer_id' => $customer->id,
            'sale_date' => now()->toDateString(),
            'items' => [
                ['animal_id' => $animal->id, 'sale_weight_kg' => 400, 'price_per_kg' => 3.5],
            ],
        ]);

        $response->assertForbidden();
    }
}
