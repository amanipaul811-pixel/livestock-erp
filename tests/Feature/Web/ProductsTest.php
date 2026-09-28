<?php

namespace Tests\Feature\Web;

use App\Models\Animal;
use App\Models\Batch;
use App\Models\HealthRecord;
use App\Models\Species;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_it_lists_only_animals_currently_on_feed(): void
    {
        $species = Species::factory()->create(['default_price_per_kg' => 3.5]);
        $batch = Batch::factory()->create(['species_id' => $species->id]);
        $onFeed = Animal::factory()->create(['batch_id' => $batch->id, 'species_id' => $species->id, 'status' => 'on_feed', 'entry_weight_kg' => 200]);
        $sold = Animal::factory()->create(['batch_id' => $batch->id, 'species_id' => $species->id, 'status' => 'sold']);
        $dead = Animal::factory()->create(['batch_id' => $batch->id, 'species_id' => $species->id, 'status' => 'dead']);

        $response = $this->actingAs($this->adminUser())->get('/products');

        $response->assertOk();
        $response->assertSee($onFeed->tag_id);
        $response->assertDontSee($sold->tag_id);
        $response->assertDontSee($dead->tag_id);
    }

    public function test_selling_price_is_weight_times_the_species_default_price(): void
    {
        $species = Species::factory()->create(['default_price_per_kg' => 4]);
        $batch = Batch::factory()->create(['species_id' => $species->id]);
        $animal = Animal::factory()->create([
            'batch_id' => $batch->id,
            'species_id' => $species->id,
            'status' => 'on_feed',
            'entry_weight_kg' => 300,
        ]);

        $response = $this->actingAs($this->adminUser())->get('/products');

        $response->assertOk();
        $response->assertSee(number_format(300 * 4, 2));
    }

    public function test_an_animal_with_no_default_price_prompts_to_set_one_instead_of_a_price(): void
    {
        $species = Species::factory()->create(['default_price_per_kg' => null]);
        $batch = Batch::factory()->create(['species_id' => $species->id]);
        Animal::factory()->create(['batch_id' => $batch->id, 'species_id' => $species->id, 'status' => 'on_feed']);

        $response = $this->actingAs($this->adminUser())->get('/products');

        $response->assertOk();
        $response->assertSee('No default price set');
    }

    public function test_it_shows_the_most_recent_health_record(): void
    {
        $species = Species::factory()->create();
        $batch = Batch::factory()->create(['species_id' => $species->id]);
        $animal = Animal::factory()->create(['batch_id' => $batch->id, 'species_id' => $species->id, 'status' => 'on_feed']);
        HealthRecord::factory()->create(['animal_id' => $animal->id, 'record_type' => 'vaccination', 'record_date' => now()->subDays(10)]);
        HealthRecord::factory()->create(['animal_id' => $animal->id, 'record_type' => 'checkup', 'record_date' => now()->subDay()]);

        $response = $this->actingAs($this->adminUser())->get('/products');

        $response->assertOk();
        $response->assertSee('Checkup on '.now()->subDay()->format('Y-m-d'));
    }

    public function test_the_products_link_is_visible_in_the_sidebar(): void
    {
        $response = $this->actingAs($this->adminUser())->get('/dashboard');

        $response->assertOk()->assertSee('Products');
    }
}
