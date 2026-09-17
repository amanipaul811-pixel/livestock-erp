<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\UpdateSpeciesPricingRequest;
use App\Models\Species;

class SpeciesController extends Controller
{
    public function index()
    {
        return view('species.index', [
            'speciesList' => Species::orderBy('name')->get(),
        ]);
    }

    public function updatePricing(UpdateSpeciesPricingRequest $request, Species $species)
    {
        $species->update($request->validated());

        return redirect()->route('species.index')->with('status', "Default price for {$species->name} updated.");
    }
}
