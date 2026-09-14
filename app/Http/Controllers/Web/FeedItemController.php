<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FeedItem;
use Illuminate\Http\Request;

class FeedItemController extends Controller
{
    public function index()
    {
        return view('feed-items.index', ['feedItems' => FeedItem::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'unit' => 'required|in:kg,bag,liter',
            'cost_per_unit' => 'required|numeric|min:0',
            'reorder_level' => 'nullable|numeric|min:0',
        ]);

        FeedItem::create($validated);

        return redirect()->route('feed-items.index')->with('status', 'Feed item added.');
    }
}
