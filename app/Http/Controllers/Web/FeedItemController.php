<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\RestockFeedItemRequest;
use App\Http\Requests\Web\StoreFeedItemRequest;
use App\Models\FeedItem;
use App\Models\FeedStockMovement;
use App\Models\Warehouse;

class FeedItemController extends Controller
{
    public function index()
    {
        return view('feed-items.index', [
            'feedItems' => FeedItem::with('warehouse')->orderBy('name')->get(),
            'warehouses' => Warehouse::orderBy('name')->get(),
        ]);
    }

    public function restock(RestockFeedItemRequest $request, FeedItem $feedItem)
    {
        FeedStockMovement::create([
            'feed_item_id' => $feedItem->id,
            'type' => 'in',
            'quantity_kg' => $request->validated('quantity_kg'),
            'reason' => $request->validated('reason') ?? 'Restock',
            'recorded_by' => $request->user()->id,
            'occurred_at' => now(),
        ]);

        return redirect()->route('feed-items.index')->with('status', 'Feed item restocked.');
    }

    public function store(StoreFeedItemRequest $request)
    {
        FeedItem::create($request->validated());

        return redirect()->route('feed-items.index')->with('status', 'Feed item added.');
    }

    public function edit(FeedItem $feedItem)
    {
        return view('feed-items.edit', [
            'feedItem' => $feedItem,
            'warehouses' => Warehouse::orderBy('name')->get(),
        ]);
    }

    public function update(StoreFeedItemRequest $request, FeedItem $feedItem)
    {
        $feedItem->update($request->validated());

        return redirect()->route('feed-items.index')->with('status', 'Feed item updated.');
    }

    public function destroy(FeedItem $feedItem)
    {
        if ($feedItem->feedLogs()->exists() || $feedItem->rationFormulaItems()->exists()) {
            return back()->withErrors(['feed_item' => 'Cannot delete a feed item that has feed logs or ration formulas using it.']);
        }

        $feedItem->delete();

        return redirect()->route('feed-items.index')->with('status', 'Feed item deleted.');
    }
}
