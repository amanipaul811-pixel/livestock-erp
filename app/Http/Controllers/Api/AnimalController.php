<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use Illuminate\Http\Request;

class AnimalController extends Controller
{
    // GET /api/batches/{batch}/animals
    public function index(int $batchId)
    {
        $animals = Animal::where('batch_id', $batchId)->with(['species', 'currentPen'])->get();

        return response()->json($animals->map(fn (Animal $a) => array_merge($a->toArray(), [
            'latest_weight_kg' => $a->latestWeightKg(),
            'average_daily_gain_kg' => $a->averageDailyGainKg(),
            'ready_to_sell' => $a->isReadyToSell(),
        ])));
    }

    // POST /api/animals — register a new animal into a batch (intake)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tag_id' => 'required|string|unique:animals,tag_id',
            'batch_id' => 'required|exists:batches,id',
            'species_id' => 'required|exists:species,id',
            'breed' => 'nullable|string',
            'sex' => 'required|in:male,female',
            'estimated_age_months' => 'nullable|integer',
            'entry_date' => 'required|date',
            'entry_weight_kg' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'current_pen_id' => 'nullable|exists:pens,id',
        ]);

        $validated['status'] = 'on_feed';

        $animal = Animal::create($validated);

        return response()->json($animal, 201);
    }

    // GET /api/animals/{animal}
    public function show(Animal $animal)
    {
        $animal->load(['species', 'batch', 'currentPen', 'weighIns', 'healthRecords']);

        return response()->json(array_merge($animal->toArray(), [
            'latest_weight_kg' => $animal->latestWeightKg(),
            'average_daily_gain_kg' => $animal->averageDailyGainKg(),
            'ready_to_sell' => $animal->isReadyToSell(),
        ]));
    }

    // GET /api/animals/ready-to-sell — flags across all active batches
    public function readyToSell()
    {
        $candidates = Animal::where('status', 'on_feed')
            ->with(['species', 'batch'])
            ->get()
            ->filter(fn (Animal $a) => $a->isReadyToSell())
            ->values();

        return response()->json($candidates);
    }
}
