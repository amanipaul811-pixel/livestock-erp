<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreExpenseRequest;
use App\Models\Batch;
use App\Models\Expense;

class ExpenseController extends Controller
{
    public function store(StoreExpenseRequest $request, Batch $batch)
    {
        $validated = $request->validated();
        $validated['batch_id'] = $batch->id;
        $validated['recorded_by'] = $request->user()->id;

        Expense::create($validated);

        return redirect()->route('batches.show', $batch)->with('status', 'Expense added.');
    }
}
