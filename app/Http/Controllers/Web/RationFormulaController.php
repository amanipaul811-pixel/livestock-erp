<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FeedItem;
use App\Models\RationFormula;
use App\Models\RationFormulaItem;
use App\Models\Species;
use Illuminate\Http\Request;
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
}
