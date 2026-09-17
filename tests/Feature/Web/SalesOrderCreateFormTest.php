<?php

namespace Tests\Feature\Web;

use App\Models\Animal;
use App\Models\Species;
use App\Models\WeighIn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class SalesOrderCreateFormTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_the_animal_option_carries_its_latest_weight_and_species_default_price(): void
    {
        $species = Species::factory()->create(['default_price_per_kg' => 3.75]);
        $animal = Animal::factory()->create(['species_id' => $species->id, 'status' => 'on_feed', 'entry_weight_kg' => 250]);
        WeighIn::factory()->create(['animal_id' => $animal->id, 'weight_kg' => 340, 'weigh_date' => now()]);

        $response = $this->actingAs($this->userWithRole('Sales'))->get('/sales-orders/create');

        $response->assertOk();
        $response->assertSee('data-weight="340"', false);
        $response->assertSee('data-price="3.75"', false);
    }

    public function test_an_animal_whose_species_has_no_default_price_carries_an_empty_price_attribute(): void
    {
        $species = Species::factory()->create(['default_price_per_kg' => null]);
        Animal::factory()->create(['species_id' => $species->id, 'status' => 'on_feed', 'tag_id' => 'NOPRICE-1']);

        $response = $this->actingAs($this->userWithRole('Sales'))->get('/sales-orders/create');

        $response->assertOk();
        $response->assertSee('NOPRICE-1');
        $response->assertSee('data-price=""', false);
    }
}
