<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreBatchRequest;
use App\Http\Requests\Web\UpdateBatchRequest;
use App\Models\Batch;
use App\Models\FeedItem;
use App\Models\Pen;
use App\Models\Species;

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

    public function store(StoreBatchRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = $request->user()->id;
        $validated['status'] = 'active';

        $batch = Batch::create($validated);

        return redirect()->route('batches.show', $batch)->with('status', "Batch {$batch->batch_code} created.");
    }

    public function show(Batch $batch)
    {
        $batch->load(['species', 'pen', 'animals.species', 'feedLogs.feedItem', 'expenses']);

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

    public function edit(Batch $batch)
    {
        return view('batches.edit', [
            'batch' => $batch,
            'pens' => Pen::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateBatchRequest $request, Batch $batch)
    {
        $batch->update($request->validated());

        return redirect()->route('batches.show', $batch)->with('status', 'Batch updated.');
    }
}
