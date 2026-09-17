<?php

namespace Tests\Feature\Web;

use App\Models\Species;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class SpeciesPricingTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_it_lists_species_with_their_default_price(): void
    {
        $species = Species::factory()->create(['name' => 'Cattle', 'default_price_per_kg' => 3.5]);

        $response = $this->actingAs($this->adminUser())->get('/species');

        $response->assertOk();
        $response->assertSee('Cattle');
        $response->assertSee('3.5');
    }

    public function test_a_user_with_salesorder_create_can_set_the_default_price(): void
    {
        $species = Species::factory()->create(['default_price_per_kg' => null]);

        $response = $this->actingAs($this->userWithRole('Sales'))
            ->put("/species/{$species->id}/pricing", ['default_price_per_kg' => 4.25]);

        $response->assertRedirect(route('species.index'));
        $this->assertSame(4.25, $species->fresh()->default_price_per_kg);
    }

    public function test_a_feeder_cannot_set_the_default_price(): void
    {
        $species = Species::factory()->create();

        $response = $this->actingAs($this->userWithRole('Feeder'))
            ->put("/species/{$species->id}/pricing", ['default_price_per_kg' => 4.25]);

        $response->assertForbidden();
    }

    public function test_the_default_price_can_be_cleared(): void
    {
        $species = Species::factory()->create(['default_price_per_kg' => 3.5]);

        $this->actingAs($this->adminUser())->put("/species/{$species->id}/pricing", ['default_price_per_kg' => '']);

        $this->assertNull($species->fresh()->default_price_per_kg);
    }
}
