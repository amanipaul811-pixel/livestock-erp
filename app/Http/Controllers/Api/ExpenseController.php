<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreExpenseRequest;
use App\Models\Batch;
use App\Models\Expense;

class ExpenseController extends Controller
{
    // GET /api/batches/{batch}/expenses
    public function index(Batch $batch)
    {
        return response()->json($batch->expenses()->orderBy('expense_date')->get());
    }

    // POST /api/batches/{batch}/expenses
    public function store(StoreExpenseRequest $request, Batch $batch)
    {
        $validated = $request->validated();
        $validated['batch_id'] = $batch->id;
        $validated['recorded_by'] = $request->user()->id;

        $expense = Expense::create($validated);

        return response()->json($expense, 201);
    }
}
