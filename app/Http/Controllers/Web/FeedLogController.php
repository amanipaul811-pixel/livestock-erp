<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\FeedItem;
use App\Models\FeedLog;
use Illuminate\Http\Request;

class FeedLogController extends Controller
{
    public function store(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'feed_item_id' => 'required|exists:feed_items,id',
            'feed_date' => 'required|date',
            'quantity_kg' => 'required|numeric|min:0',
        ]);

        $feedItem = FeedItem::findOrFail($validated['feed_item_id']);

        $validated['batch_id'] = $batch->id;
        $validated['total_cost'] = $validated['quantity_kg'] * $feedItem->cost_per_unit;
        $validated['recorded_by'] = $request->user()->id;

        FeedLog::create($validated);

        return redirect()->route('batches.show', $batch)->with('status', 'Feed log added.');
    }
}
