<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreFeedLogRequest;
use App\Models\Batch;
use App\Models\FeedItem;
use App\Models\FeedLog;
use App\Models\FeedStockMovement;

class FeedLogController extends Controller
{
    public function store(StoreFeedLogRequest $request, Batch $batch)
    {
        $validated = $request->validated();
        $feedItem = FeedItem::findOrFail($validated['feed_item_id']);

        $validated['batch_id'] = $batch->id;
        $validated['total_cost'] = $validated['quantity_kg'] * $feedItem->cost_per_unit;
        $validated['recorded_by'] = $request->user()->id;

        FeedLog::create($validated);

        FeedStockMovement::create([
            'feed_item_id' => $feedItem->id,
            'type' => 'out',
            'quantity_kg' => $validated['quantity_kg'],
            'reason' => 'Feed log for batch '.$batch->batch_code,
            'recorded_by' => $request->user()->id,
            'occurred_at' => $validated['feed_date'],
        ]);

        return redirect()->route('batches.show', $batch)->with('status', 'Feed log added.');
    }
}
