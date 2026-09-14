<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    // GET /api/warehouses
    public function index()
    {
        return response()->json(Warehouse::orderBy('name')->get());
    }

    // POST /api/warehouses
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'location' => 'nullable|string|max:150',
            'type' => 'required|in:feed,medicine,equipment,general',
        ]);

        $warehouse = Warehouse::create($validated);

        return response()->json($warehouse, 201);
    }
}
