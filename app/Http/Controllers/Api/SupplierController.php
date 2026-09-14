<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    // GET /api/suppliers
    public function index()
    {
        return response()->json(Supplier::orderBy('name')->get());
    }

    // POST /api/suppliers
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:255',
            'supplier_type' => 'required|in:animal,feed,medicine,other',
        ]);

        $supplier = Supplier::create($validated);

        return response()->json($supplier, 201);
    }
}
