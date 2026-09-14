<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
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

        Expense::create($validated);

        return redirect()->route('batches.show', $batch)->with('status', 'Expense added.');
    }
}
