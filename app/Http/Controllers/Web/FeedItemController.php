<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FeedItem;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class FeedItemController extends Controller
{
    public function index()
    {
        return view('feed-items.index', [
            'feedItems' => FeedItem::with('warehouse')->orderBy('name')->get(),
            'warehouses' => Warehouse::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'unit' => 'required|in:kg,bag,liter',
            'cost_per_unit' => 'required|numeric|min:0',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'reorder_level' => 'nullable|numeric|min:0',
        ]);

        FeedItem::create($validated);

        return redirect()->route('feed-items.index')->with('status', 'Feed item added.');
    }
}
