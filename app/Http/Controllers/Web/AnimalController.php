<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Batch;
use App\Models\Pen;
use App\Models\Supplier;
use Illuminate\Http\Request;

class AnimalController extends Controller
{
    public function create(Batch $batch)
    {
        return view('animals.create', [
            'batch' => $batch,
            'pens' => Pen::where('is_active', true)->orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'tag_id' => 'required|string|unique:animals,tag_id',
            'breed' => 'nullable|string',
            'sex' => 'required|in:male,female',
            'estimated_age_months' => 'nullable|integer',
            'entry_date' => 'required|date',
            'entry_weight_kg' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'current_pen_id' => 'nullable|exists:pens,id',
        ]);

        $validated['batch_id'] = $batch->id;
        $validated['species_id'] = $batch->species_id;
        $validated['status'] = 'on_feed';

        $animal = Animal::create($validated);

        return redirect()->route('animals.show', $animal)->with('status', "Animal {$animal->tag_id} added to batch.");
    }

    public function show(Animal $animal)
    {
        $animal->load(['species', 'batch', 'currentPen', 'supplier', 'weighIns', 'healthRecords']);

        return view('animals.show', [
            'animal' => $animal,
            'latestWeight' => $animal->latestWeightKg(),
            'adg' => $animal->averageDailyGainKg(),
            'readyToSell' => $animal->isReadyToSell(),
        ]);
    }
}
