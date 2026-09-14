<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WeighIn;
use Illuminate\Http\Request;

class WeighInController extends Controller
{
    // POST /api/animals/{animal}/weigh-ins — log a periodic weigh-in
    public function store(Request $request, int $animalId)
    {
        $validated = $request->validate([
            'weigh_date' => 'required|date',
            'weight_kg' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['animal_id'] = $animalId;
        $validated['recorded_by'] = $request->user()->id ?? null;

        $weighIn = WeighIn::create($validated);

        return response()->json($weighIn, 201);
    }

    // GET /api/animals/{animal}/weigh-ins — growth history for the weight chart
    public function index(int $animalId)
    {
        $weighIns = WeighIn::where('animal_id', $animalId)->orderBy('weigh_date')->get();

        return response()->json($weighIns);
    }
}
