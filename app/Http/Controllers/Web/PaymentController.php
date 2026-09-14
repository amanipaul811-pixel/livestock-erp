<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\SalesOrder;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, SalesOrder $salesOrder)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01|max:'.$salesOrder->balanceDue(),
            'method' => 'required|in:cash,bank_transfer,mobile_money,cheque',
            'notes' => 'nullable|string',
        ]);

        $validated['reference_type'] = 'sales_order';
        $validated['reference_id'] = $salesOrder->id;

        Payment::create($validated);

        return redirect()->route('sales-orders.show', $salesOrder)->with('status', 'Payment recorded.');
    }
}
