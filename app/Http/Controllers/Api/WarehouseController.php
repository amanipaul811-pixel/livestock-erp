<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreWarehouseRequest;
use App\Models\Warehouse;

class WarehouseController extends Controller
{
    // GET /api/warehouses
    public function index()
    {
        return response()->json(Warehouse::orderBy('name')->get());
    }

    // POST /api/warehouses
    public function store(StoreWarehouseRequest $request)
    {
        $warehouse = Warehouse::create($request->validated());

        return response()->json($warehouse, 201);
    }
}
