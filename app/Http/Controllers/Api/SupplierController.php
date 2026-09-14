<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSupplierRequest;
use App\Models\Supplier;

class SupplierController extends Controller
{
    // GET /api/suppliers
    public function index()
    {
        return response()->json(Supplier::orderBy('name')->get());
    }

    // POST /api/suppliers
    public function store(StoreSupplierRequest $request)
    {
        $supplier = Supplier::create($request->validated());

        return response()->json($supplier, 201);
    }
}
