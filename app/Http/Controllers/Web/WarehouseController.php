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

    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.edit', ['warehouse' => $warehouse]);
    }

    public function update(StoreWarehouseRequest $request, Warehouse $warehouse)
    {
        $warehouse->update($request->validated());

        return redirect()->route('warehouses.index')->with('status', 'Warehouse updated.');
    }

    public function destroy(Warehouse $warehouse)
    {
        if ($warehouse->feedItems()->exists()) {
            return back()->withErrors(['warehouse' => 'Cannot delete a warehouse with feed items assigned to it.']);
        }

        $warehouse->delete();

        return redirect()->route('warehouses.index')->with('status', 'Warehouse deleted.');
    }
}
