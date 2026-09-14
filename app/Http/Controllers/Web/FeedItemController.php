<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreFeedItemRequest;
use App\Models\FeedItem;
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

    public function store(StoreFeedItemRequest $request)
    {
        FeedItem::create($request->validated());

        return redirect()->route('feed-items.index')->with('status', 'Feed item added.');
    }
}
