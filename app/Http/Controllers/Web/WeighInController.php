<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreWeighInRequest;
use App\Models\Animal;
use App\Models\WeighIn;

class WeighInController extends Controller
{
    public function store(StoreWeighInRequest $request, Animal $animal)
    {
        $validated = $request->validated();
        $validated['animal_id'] = $animal->id;
        $validated['recorded_by'] = $request->user()->id;

        WeighIn::create($validated);

        return redirect()->route('animals.show', $animal)->with('status', 'Weigh-in recorded.');
    }
}
