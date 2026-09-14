<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        return view('warehouses.index', ['warehouses' => Warehouse::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'location' => 'nullable|string|max:150',
            'type' => 'required|in:feed,medicine,equipment,general',
        ]);

        Warehouse::create($validated);

        return redirect()->route('warehouses.index')->with('status', 'Warehouse added.');
    }
}
