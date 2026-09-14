<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreHealthRecordRequest;
use App\Models\Animal;
use App\Models\HealthRecord;

class HealthRecordController extends Controller
{
    public function store(StoreHealthRecordRequest $request, Animal $animal)
    {
        $validated = $request->validated();
        $validated['animal_id'] = $animal->id;
        $validated['performed_by'] = $request->user()->id;

        HealthRecord::create($validated);

        if ($validated['record_type'] === 'death') {
            $animal->update(['status' => 'dead']);
        }

        return redirect()->route('animals.show', $animal)->with('status', 'Health record added.');
    }
}
