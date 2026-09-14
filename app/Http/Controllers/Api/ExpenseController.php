<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    // GET /api/batches/{batch}/expenses
    public function index(Batch $batch)
    {
        return response()->json($batch->expenses()->orderBy('expense_date')->get());
    }

    // POST /api/batches/{batch}/expenses
    public function store(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'category' => 'required|in:labor,utilities,transport,rent,other',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['batch_id'] = $batch->id;
        $validated['recorded_by'] = $request->user()->id;

        $expense = Expense::create($validated);

        return response()->json($expense, 201);
    }
}
