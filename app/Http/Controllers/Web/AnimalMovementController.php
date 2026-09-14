<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\AnimalMovement;
use Illuminate\Http\Request;

class AnimalMovementController extends Controller
{
    public function store(Request $request, Animal $animal)
    {
        $validated = $request->validate([
            'to_pen_id' => 'required|exists:pens,id',
            'move_date' => 'required|date',
            'reason' => 'nullable|string|max:150',
        ]);

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
