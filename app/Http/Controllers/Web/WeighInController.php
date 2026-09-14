<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\WeighIn;
use Illuminate\Http\Request;

class WeighInController extends Controller
{
    public function store(Request $request, Animal $animal)
    {
        $validated = $request->validate([
            'weigh_date' => 'required|date',
            'weight_kg' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['animal_id'] = $animal->id;
        $validated['recorded_by'] = $request->user()->id;

        WeighIn::create($validated);

        return redirect()->route('animals.show', $animal)->with('status', 'Weigh-in recorded.');
    }
}
