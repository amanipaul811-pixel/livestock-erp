<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreWeighInRequest;
use App\Models\Animal;
use App\Models\WeighIn;

class WeighInController extends Controller
{
    // POST /api/animals/{animal}/weigh-ins — log a periodic weigh-in
    public function store(StoreWeighInRequest $request, Animal $animal)
    {
        $validated = $request->validated();
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
