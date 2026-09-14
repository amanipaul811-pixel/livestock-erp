<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\HealthRecord;
use Illuminate\Http\Request;

class HealthRecordController extends Controller
{
    // POST /api/animals/{animal}/health-records
    public function store(Request $request, int $animalId)
    {
        $validated = $request->validate([
            'record_type' => 'required|in:vaccination,deworming,treatment,checkup,death',
            'record_date' => 'required|date',
            'description' => 'nullable|string',
            'medicine_used' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'cause_of_death' => 'nullable|string|required_if:record_type,death',
        ]);

        $validated['animal_id'] = $animalId;
        $validated['performed_by'] = $request->user()->id ?? null;

        $record = HealthRecord::create($validated);

        // A death record retires the animal from active feeding automatically
        if ($validated['record_type'] === 'death') {
            Animal::where('id', $animalId)->update(['status' => 'dead']);
        }

        return response()->json($record, 201);
    }

    // GET /api/animals/{animal}/health-records
    public function index(int $animalId)
    {
        return response()->json(
            HealthRecord::where('animal_id', $animalId)->orderBy('record_date')->get()
        );
    }
}
