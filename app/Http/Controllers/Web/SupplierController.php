<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        return view('suppliers.index', ['suppliers' => Supplier::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'supplier_type' => 'required|in:animal,feed,medicine,other',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('status', 'Supplier added.');
    }
}
