<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeedItem;
use App\Models\FeedLog;
use Illuminate\Http\Request;

class FeedLogController extends Controller
{
    // POST /api/batches/{batch}/feed-logs — log daily feed given to a batch/pen
    public function store(Request $request, int $batchId)
    {
        $validated = $request->validate([
            'feed_item_id' => 'required|exists:feed_items,id',
            'feed_date' => 'required|date',
            'quantity_kg' => 'required|numeric|min:0',
        ]);

        $feedItem = FeedItem::findOrFail($validated['feed_item_id']);

        $validated['batch_id'] = $batchId;
        $validated['total_cost'] = $validated['quantity_kg'] * $feedItem->cost_per_unit;
        $validated['recorded_by'] = $request->user()->id ?? null;

        $feedLog = FeedLog::create($validated);

        return response()->json($feedLog, 201);
    }

    // GET /api/batches/{batch}/feed-logs
    public function index(int $batchId)
    {
        $logs = FeedLog::where('batch_id', $batchId)->with('feedItem')->orderBy('feed_date')->get();

        return response()->json($logs);
    }
}
