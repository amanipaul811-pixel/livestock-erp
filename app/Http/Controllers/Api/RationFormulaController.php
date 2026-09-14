<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RationFormula;
use App\Models\RationFormulaItem;
use Illuminate\Http\Request;
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'species_id' => 'required|exists:species,id',
            'stage' => 'required|in:starter,growing,finishing',
            'items' => 'required|array|min:1',
            'items.*.feed_item_id' => 'required|exists:feed_items,id',
            'items.*.quantity_kg_per_head' => 'required|numeric|min:0.01',
        ]);

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
