<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Batch;
use App\Models\Expense;
use App\Models\FeedLog;
use App\Models\SalesOrder;
use App\Models\Species;
use App\Models\WeighIn;
use Illuminate\Support\Carbon;

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

        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->startOfMonth());
        $monthLabels = $months->map(fn (Carbon $m) => $m->format('M Y'))->values();

        $salesOrders = SalesOrder::select('sale_date', 'total_amount')->get();
        $expenses = Expense::select('expense_date', 'amount')->get();
        $feedLogs = FeedLog::select('feed_date', 'total_cost')->get();

        $revenueByMonth = $months->map(
            fn (Carbon $month) => (float) $salesOrders
                ->filter(fn ($s) => $s->sale_date->isSameMonth($month))
                ->sum('total_amount')
        )->values();

        $expenseByMonth = $months->map(function (Carbon $month) use ($expenses, $feedLogs) {
            $recorded = $expenses->filter(fn ($e) => $e->expense_date->isSameMonth($month))->sum('amount');
            $feed = $feedLogs->filter(fn ($f) => $f->feed_date->isSameMonth($month))->sum('total_cost');

            return (float) ($recorded + $feed);
        })->values();

        $weighIns = WeighIn::with('animal.species')->get();
        $weightTrend = Species::pluck('name')->map(function (string $name) use ($months, $weighIns) {
            $data = $months->map(function (Carbon $month) use ($weighIns, $name) {
                $matching = $weighIns->filter(
                    fn ($w) => $w->weigh_date->isSameMonth($month) && $w->animal->species->name === $name
                );

                return $matching->isEmpty() ? null : round($matching->avg('weight_kg'), 1);
            })->values();

            return ['label' => $name, 'data' => $data];
        })->filter(fn ($series) => $series['data']->contains(fn ($v) => $v !== null))->values();

        $recentBatches = Batch::latest('start_date')->take(8)->get()->reverse()->values();
        $batchProfitLabels = $recentBatches->pluck('batch_code');
        $batchProfitData = $recentBatches->map(fn (Batch $b) => round($b->netProfit(), 2));

        return view('dashboard', [
            'activeBatches' => Batch::where('status', 'active')->count(),
            'animalsOnFeed' => $onFeed->count(),
            'avgAdg' => $avgAdg,
            'readyToSell' => $readyToSell,
            'speciesBreakdown' => $speciesBreakdown,
            'chartMonthLabels' => $monthLabels,
            'chartRevenue' => $revenueByMonth,
            'chartExpenses' => $expenseByMonth,
            'chartWeightTrend' => $weightTrend,
            'chartBatchLabels' => $batchProfitLabels,
            'chartBatchProfit' => $batchProfitData,
        ]);
    }
}
