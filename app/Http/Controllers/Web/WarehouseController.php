<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreWarehouseRequest;
use App\Models\Warehouse;

class WarehouseController extends Controller
{
    public function index()
    {
        return view('warehouses.index', ['warehouses' => Warehouse::orderBy('name')->get()]);
    }

    public function store(StoreWarehouseRequest $request)
    {
        Warehouse::create($request->validated());

        return redirect()->route('warehouses.index')->with('status', 'Warehouse added.');
    }
}
