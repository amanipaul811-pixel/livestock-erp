<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\HealthRecord;
use Illuminate\Http\Request;

class HealthRecordController extends Controller
{
    public function store(Request $request, Animal $animal)
    {
        $validated = $request->validate([
            'record_type' => 'required|in:vaccination,deworming,treatment,checkup,death',
            'record_date' => 'required|date',
            'description' => 'nullable|string',
            'medicine_used' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'cause_of_death' => 'nullable|string|required_if:record_type,death',
        ]);

        $validated['animal_id'] = $animal->id;
        $validated['performed_by'] = $request->user()->id;

        HealthRecord::create($validated);

        if ($validated['record_type'] === 'death') {
            $animal->update(['status' => 'dead']);
        }

        return redirect()->route('animals.show', $animal)->with('status', 'Health record added.');
    }
}
