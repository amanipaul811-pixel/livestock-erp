<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreAnimalMovementRequest;
use App\Models\Animal;
use App\Models\AnimalMovement;

class AnimalMovementController extends Controller
{
    public function store(StoreAnimalMovementRequest $request, Animal $animal)
    {
        $validated = $request->validated();

        if ((int) $validated['to_pen_id'] === $animal->current_pen_id) {
            return back()->withErrors(['to_pen_id' => 'Animal is already in that pen.']);
        }

        AnimalMovement::create([
            'animal_id' => $animal->id,
            'from_pen_id' => $animal->current_pen_id,
            'to_pen_id' => $validated['to_pen_id'],
            'move_date' => $validated['move_date'],
            'reason' => $validated['reason'] ?? null,
        ]);

        $animal->update(['current_pen_id' => $validated['to_pen_id']]);

        return redirect()->route('animals.show', $animal)->with('status', 'Animal moved to new pen.');
    }
}
