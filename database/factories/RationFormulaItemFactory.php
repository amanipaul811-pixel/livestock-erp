<?php

namespace Database\Factories;

use App\Models\FeedItem;
use App\Models\RationFormula;
use App\Models\RationFormulaItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RationFormulaItem>
 */
class RationFormulaItemFactory extends Factory
{
    protected $model = RationFormulaItem::class;

    public function definition(): array
    {
        return [
            'ration_formula_id' => RationFormula::factory(),
            'feed_item_id' => FeedItem::factory(),
            'quantity_kg_per_head' => 5,
        ];
    }
}
