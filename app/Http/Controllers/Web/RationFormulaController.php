<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreRationFormulaRequest;
use App\Models\FeedItem;
use App\Models\RationFormula;
use App\Models\RationFormulaItem;
use App\Models\Species;
use Illuminate\Support\Facades\DB;

class RationFormulaController extends Controller
{
    public function index()
    {
        return view('ration-formulas.index', [
            'formulas' => RationFormula::with('species')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('ration-formulas.create', [
            'speciesList' => Species::orderBy('name')->get(),
            'feedItems' => FeedItem::orderBy('name')->get(),
        ]);
    }

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

        return redirect()->route('ration-formulas.show', $formula)->with('status', "Ration formula {$formula->name} created.");
    }

    public function show(RationFormula $rationFormula)
    {
        $rationFormula->load(['species', 'items.feedItem']);

        return view('ration-formulas.show', [
            'formula' => $rationFormula,
            'dailyCostPerHead' => $rationFormula->dailyCostPerHead(),
        ]);
    }

    public function edit(RationFormula $rationFormula)
    {
        $rationFormula->load('items');

        return view('ration-formulas.edit', [
            'formula' => $rationFormula,
            'speciesList' => Species::orderBy('name')->get(),
            'feedItems' => FeedItem::orderBy('name')->get(),
        ]);
    }

    public function update(StoreRationFormulaRequest $request, RationFormula $rationFormula)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $rationFormula) {
            $rationFormula->update([
                'name' => $validated['name'],
                'species_id' => $validated['species_id'],
                'stage' => $validated['stage'],
            ]);

            $rationFormula->items()->delete();

            foreach ($validated['items'] as $item) {
                RationFormulaItem::create([
                    'ration_formula_id' => $rationFormula->id,
                    'feed_item_id' => $item['feed_item_id'],
                    'quantity_kg_per_head' => $item['quantity_kg_per_head'],
                ]);
            }
        });

        return redirect()->route('ration-formulas.show', $rationFormula)->with('status', 'Ration formula updated.');
    }

    public function destroy(RationFormula $rationFormula)
    {
        $rationFormula->delete();

        return redirect()->route('ration-formulas.index')->with('status', 'Ration formula deleted.');
    }
}
