<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\AnimalMovement;
use Illuminate\Http\Request;

class AnimalMovementController extends Controller
{
    // GET /api/animals/{animal}/movements
    public function index(Animal $animal)
    {
        return response()->json($animal->movements()->with(['fromPen', 'toPen'])->orderBy('move_date')->get());
    }

    // POST /api/animals/{animal}/movements
    public function store(Request $request, Animal $animal)
    {
        $validated = $request->validate([
            'to_pen_id' => 'required|exists:pens,id',
            'move_date' => 'required|date',
            'reason' => 'nullable|string|max:150',
        ]);

        if ((int) $validated['to_pen_id'] === $animal->current_pen_id) {
            return response()->json(['message' => 'Animal is already in that pen.'], 422);
        }

        $movement = AnimalMovement::create([
            'animal_id' => $animal->id,
            'from_pen_id' => $animal->current_pen_id,
            'to_pen_id' => $validated['to_pen_id'],
            'move_date' => $validated['move_date'],
            'reason' => $validated['reason'] ?? null,
        ]);

        $animal->update(['current_pen_id' => $validated['to_pen_id']]);

        return response()->json($movement, 201);
    }
}
