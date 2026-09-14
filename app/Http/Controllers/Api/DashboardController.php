<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Batch;

class DashboardController extends Controller
{
    // GET /api/dashboard — top-line KPIs shown on the main dashboard
    public function index()
    {
        $onFeed = Animal::where('status', 'on_feed')->get();

        $avgAdg = $onFeed->isEmpty() ? 0 : round(
            $onFeed->avg(fn (Animal $a) => $a->averageDailyGainKg()), 2
        );

        $readyToSell = $onFeed->filter(fn (Animal $a) => $a->isReadyToSell())->count();

        return response()->json([
            'active_batches' => Batch::where('status', 'active')->count(),
            'animals_on_feed' => $onFeed->count(),
            'average_daily_gain_kg' => $avgAdg,
            'ready_to_sell_head' => $readyToSell,
            'species_breakdown' => Animal::where('status', 'on_feed')
                ->join('species', 'animals.species_id', '=', 'species.id')
                ->selectRaw('species.name as species, count(*) as head_count')
                ->groupBy('species.name')
                ->get(),
        ]);
    }
}
