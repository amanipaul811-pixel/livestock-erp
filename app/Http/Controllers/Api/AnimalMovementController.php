<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreAnimalMovementRequest;
use App\Models\Animal;
use App\Models\AnimalMovement;

class AnimalMovementController extends Controller
{
    // GET /api/animals/{animal}/movements
    public function index(Animal $animal)
    {
        return response()->json($animal->movements()->with(['fromPen', 'toPen'])->orderBy('move_date')->get());
    }

    // POST /api/animals/{animal}/movements
    public function store(StoreAnimalMovementRequest $request, Animal $animal)
    {
        $validated = $request->validated();

        if ((int) $validated['to_pen_id'] === $animal->current_pen_id) {
            return response()->json(['message' => 'Animal is already in that pen.'], 422);
        }

        $movement = AnimalMovement::create([
            'animal_id' => $animal->id,
            'from_pen_id' => $animal->current_pen_id,
            'to_pen_id' => $validated['to_pen_id'],
            'weight_kg_at_move' => $animal->latestWeightKg(),
            'move_date' => $validated['move_date'],
            'reason' => $validated['reason'] ?? null,
        ]);

        $animal->update(['current_pen_id' => $validated['to_pen_id']]);

        return response()->json($movement, 201);
    }
}
