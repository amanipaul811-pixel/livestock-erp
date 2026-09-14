<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreFeedLogRequest;
use App\Models\Batch;
use App\Models\FeedItem;
use App\Models\FeedLog;

class FeedLogController extends Controller
{
    // POST /api/batches/{batch}/feed-logs — log daily feed given to a batch/pen
    public function store(StoreFeedLogRequest $request, Batch $batch)
    {
        $validated = $request->validated();
        $feedItem = FeedItem::findOrFail($validated['feed_item_id']);

        $validated['batch_id'] = $batch->id;
        $validated['total_cost'] = $validated['quantity_kg'] * $feedItem->cost_per_unit;
        $validated['recorded_by'] = $request->user()->id ?? null;

        $feedLog = FeedLog::create($validated);

        return response()->json($feedLog, 201);
    }

    // GET /api/batches/{batch}/feed-logs
    public function index(Batch $batch)
    {
        return response()->json($batch->feedLogs()->with('feedItem')->orderBy('feed_date')->get());
    }
}
