<?php

namespace Tests\Feature\Api;

use App\Models\FeedItem;
use App\Models\Species;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class RationFormulaTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_farm_manager_can_create_a_ration_formula_with_items(): void
    {
        Sanctum::actingAs($this->userWithRole('Farm Manager'));
        $species = Species::factory()->create();
        $feedItem = FeedItem::factory()->create(['cost_per_unit' => 0.5]);

        $response = $this->postJson('/api/ration-formulas', [
            'name' => 'Cattle Finishing Ration',
            'species_id' => $species->id,
            'stage' => 'finishing',
            'items' => [
                ['feed_item_id' => $feedItem->id, 'quantity_kg_per_head' => 8],
            ],
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('ration_formula_items', ['feed_item_id' => $feedItem->id, 'quantity_kg_per_head' => 8]);
    }

    public function test_daily_cost_per_head_is_computed_correctly(): void
    {
        Sanctum::actingAs($this->userWithRole('Farm Manager'));
        $species = Species::factory()->create();
        $feedItem = FeedItem::factory()->create(['cost_per_unit' => 0.5]);

        $created = $this->postJson('/api/ration-formulas', [
            'name' => 'Test Ration',
            'species_id' => $species->id,
            'stage' => 'growing',
            'items' => [
                ['feed_item_id' => $feedItem->id, 'quantity_kg_per_head' => 8],
            ],
        ])->json();

        $response = $this->getJson("/api/ration-formulas/{$created['id']}");

        $response->assertOk()->assertJsonPath('daily_cost_per_head', 4);
    }

    public function test_requires_at_least_one_item(): void
    {
        Sanctum::actingAs($this->userWithRole('Farm Manager'));
        $species = Species::factory()->create();

        $response = $this->postJson('/api/ration-formulas', [
            'name' => 'Empty Ration',
            'species_id' => $species->id,
            'stage' => 'growing',
            'items' => [],
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('items');
    }
}
