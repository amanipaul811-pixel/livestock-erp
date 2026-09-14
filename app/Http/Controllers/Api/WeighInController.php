<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\WeighIn;
use Illuminate\Http\Request;

class WeighInController extends Controller
{
    // POST /api/animals/{animal}/weigh-ins — log a periodic weigh-in
    public function store(Request $request, Animal $animal)
    {
        $validated = $request->validate([
            'weigh_date' => 'required|date',
            'weight_kg' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['animal_id'] = $animal->id;
        $validated['recorded_by'] = $request->user()->id ?? null;

        $weighIn = WeighIn::create($validated);

        return response()->json($weighIn, 201);
    }

    // GET /api/animals/{animal}/weigh-ins — growth history for the weight chart
    public function index(Animal $animal)
    {
        return response()->json($animal->weighIns()->orderBy('weigh_date')->get());
    }
}
