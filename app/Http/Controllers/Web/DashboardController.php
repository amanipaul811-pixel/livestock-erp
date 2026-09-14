<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Batch;

class DashboardController extends Controller
{
    public function index()
    {
        $onFeed = Animal::where('status', 'on_feed')->with('species')->get();

        $avgAdg = $onFeed->isEmpty() ? 0 : round(
            $onFeed->avg(fn (Animal $a) => $a->averageDailyGainKg()), 2
        );

        $readyToSell = $onFeed->filter(fn (Animal $a) => $a->isReadyToSell());

        $speciesBreakdown = $onFeed->groupBy(fn (Animal $a) => $a->species->name)
            ->map(fn ($group) => $group->count());

        return view('dashboard', [
            'activeBatches' => Batch::where('status', 'active')->count(),
            'animalsOnFeed' => $onFeed->count(),
            'avgAdg' => $avgAdg,
            'readyToSell' => $readyToSell,
            'speciesBreakdown' => $speciesBreakdown,
        ]);
    }
}
