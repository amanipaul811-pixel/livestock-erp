<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreHealthRecordRequest;
use App\Models\Animal;
use App\Models\HealthRecord;

class HealthRecordController extends Controller
{
    // POST /api/animals/{animal}/health-records
    public function store(StoreHealthRecordRequest $request, Animal $animal)
    {
        $validated = $request->validated();
        $validated['animal_id'] = $animal->id;
        $validated['performed_by'] = $request->user()->id ?? null;

        $record = HealthRecord::create($validated);

        // A death record retires the animal from active feeding automatically
        if ($validated['record_type'] === 'death') {
            $animal->update(['status' => 'dead']);
        }

        return response()->json($record, 201);
    }

    // GET /api/animals/{animal}/health-records
    public function index(Animal $animal)
    {
        return response()->json($animal->healthRecords()->orderBy('record_date')->get());
    }
}
