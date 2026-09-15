<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreWeighInRequest;
use App\Models\Animal;
use App\Models\User;
use App\Models\WeighIn;
use App\Notifications\AnimalReadyToSellNotification;
use Illuminate\Support\Facades\Notification;

class WeighInController extends Controller
{
    public function store(StoreWeighInRequest $request, Animal $animal)
    {
        $validated = $request->validated();
        $validated['animal_id'] = $animal->id;
        $validated['recorded_by'] = $request->user()->id;

        $wasReady = $animal->isReadyToSell();

        WeighIn::create($validated);

        if (! $wasReady && $animal->fresh()->isReadyToSell()) {
            Notification::send(User::withPermission('salesorder.create')->get(), new AnimalReadyToSellNotification($animal));
        }

        return redirect()->route('animals.show', $animal)->with('status', 'Weigh-in recorded.');
    }
}
