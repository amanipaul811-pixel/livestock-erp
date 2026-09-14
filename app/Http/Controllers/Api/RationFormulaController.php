<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreRationFormulaRequest;
use App\Models\RationFormula;
use App\Models\RationFormulaItem;
use Illuminate\Support\Facades\DB;

class RationFormulaController extends Controller
{
    // GET /api/ration-formulas
    public function index()
    {
        return response()->json(RationFormula::with(['species', 'items.feedItem'])->orderBy('name')->get());
    }

    // POST /api/ration-formulas
    // Body: { name, species_id, stage, items: [{ feed_item_id, quantity_kg_per_head }] }
    public function store(StoreRationFormulaRequest $request)
    {
        $validated = $request->validated();

        $formula = DB::transaction(function () use ($validated) {
            $formula = RationFormula::create([
                'name' => $validated['name'],
                'species_id' => $validated['species_id'],
                'stage' => $validated['stage'],
            ]);

            foreach ($validated['items'] as $item) {
                RationFormulaItem::create([
                    'ration_formula_id' => $formula->id,
                    'feed_item_id' => $item['feed_item_id'],
                    'quantity_kg_per_head' => $item['quantity_kg_per_head'],
                ]);
            }

            return $formula;
        });

        return response()->json($formula->load('items.feedItem'), 201);
    }

    // GET /api/ration-formulas/{rationFormula}
    public function show(RationFormula $rationFormula)
    {
        $rationFormula->load(['species', 'items.feedItem']);

        return response()->json(array_merge($rationFormula->toArray(), [
            'daily_cost_per_head' => $rationFormula->dailyCostPerHead(),
        ]));
    }
}
