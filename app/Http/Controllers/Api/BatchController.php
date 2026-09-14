<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBatchRequest;
use App\Http\Requests\Api\UpdateBatchRequest;
use App\Models\Batch;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    // GET /api/batches — list with live KPIs per batch
    public function index(Request $request)
    {
        $batches = Batch::with(['species', 'pen'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->species_id, fn ($q) => $q->where('species_id', $request->species_id))
            ->get();

        return response()->json($batches->map(fn (Batch $b) => $this->withKpis($b)));
    }

    // GET /api/batches/{batch}
    public function show(Batch $batch)
    {
        $batch->load(['species', 'pen', 'animals']);

        return response()->json($this->withKpis($batch));
    }

    // POST /api/batches
    public function store(StoreBatchRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = $request->user()->id ?? null;
        $validated['status'] = 'active';

        $batch = Batch::create($validated);

        return response()->json($batch, 201);
    }

    // PATCH /api/batches/{batch}
    public function update(UpdateBatchRequest $request, Batch $batch)
    {
        $batch->update($request->validated());

        return response()->json($batch);
    }

    // GET /api/batches/{batch}/profitability
    public function profitability(Batch $batch)
    {
        return response()->json([
            'batch_code' => $batch->batch_code,
            'head_count' => $batch->animals()->count(),
            'days_on_feed' => $batch->daysOnFeed(),
            'total_purchase_cost' => $batch->totalPurchaseCost(),
            'total_feed_cost' => $batch->totalFeedCost(),
            'total_health_cost' => $batch->totalHealthCost(),
            'total_other_expenses' => $batch->totalOtherExpenses(),
            'total_sales_revenue' => $batch->totalSalesRevenue(),
            'feed_conversion_ratio' => $batch->feedConversionRatio(),
            'net_profit' => $batch->netProfit(),
        ]);
    }

    private function withKpis(Batch $batch): array
    {
        return array_merge($batch->toArray(), [
            'head_count' => $batch->animals()->count(),
            'days_on_feed' => $batch->daysOnFeed(),
            'feed_conversion_ratio' => $batch->feedConversionRatio(),
            'net_profit' => $batch->netProfit(),
        ]);
    }
}
