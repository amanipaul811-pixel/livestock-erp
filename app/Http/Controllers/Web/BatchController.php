<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\FeedItem;
use App\Models\Pen;
use App\Models\Species;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::with(['species', 'pen'])->latest('start_date')->get();

        return view('batches.index', ['batches' => $batches]);
    }

    public function create()
    {
        return view('batches.create', [
            'speciesList' => Species::orderBy('name')->get(),
            'pens' => Pen::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'batch_code' => 'required|string|unique:batches,batch_code',
            'species_id' => 'required|exists:species,id',
            'pen_id' => 'nullable|exists:pens,id',
            'start_date' => 'required|date',
            'expected_end_date' => 'nullable|date|after:start_date',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = $request->user()->id;
        $validated['status'] = 'active';

        $batch = Batch::create($validated);

        return redirect()->route('batches.show', $batch)->with('status', "Batch {$batch->batch_code} created.");
    }

    public function show(Batch $batch)
    {
        $batch->load(['species', 'pen', 'animals.species', 'feedLogs.feedItem']);

        return view('batches.show', [
            'batch' => $batch,
            'feedItems' => FeedItem::orderBy('name')->get(),
            'kpis' => [
                'head_count' => $batch->animals()->count(),
                'days_on_feed' => $batch->daysOnFeed(),
                'total_purchase_cost' => $batch->totalPurchaseCost(),
                'total_feed_cost' => $batch->totalFeedCost(),
                'total_health_cost' => $batch->totalHealthCost(),
                'total_other_expenses' => $batch->totalOtherExpenses(),
                'total_sales_revenue' => $batch->totalSalesRevenue(),
                'feed_conversion_ratio' => $batch->feedConversionRatio(),
                'net_profit' => $batch->netProfit(),
            ],
        ]);
    }
}
