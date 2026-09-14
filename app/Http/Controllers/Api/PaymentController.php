<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // GET /api/sales-orders/{salesOrder}/payments
    public function index(SalesOrder $salesOrder)
    {
        return response()->json(
            Payment::where('reference_type', 'sales_order')
                ->where('reference_id', $salesOrder->id)
                ->orderBy('payment_date')
                ->get()
        );
    }

    // POST /api/sales-orders/{salesOrder}/payments
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

        $payment = Payment::create($validated);

        return response()->json($payment, 201);
    }

    // GET /api/purchase-orders/{purchaseOrder}/payments
    public function indexForPurchaseOrder(PurchaseOrder $purchaseOrder)
    {
        return response()->json(
            Payment::where('reference_type', 'purchase_order')
                ->where('reference_id', $purchaseOrder->id)
                ->orderBy('payment_date')
                ->get()
        );
    }

    // POST /api/purchase-orders/{purchaseOrder}/payments
    public function storeForPurchaseOrder(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01|max:'.$purchaseOrder->balanceDue(),
            'method' => 'required|in:cash,bank_transfer,mobile_money,cheque',
            'notes' => 'nullable|string',
        ]);

        $validated['reference_type'] = 'purchase_order';
        $validated['reference_id'] = $purchaseOrder->id;

        $payment = Payment::create($validated);

        return response()->json($payment, 201);
    }
}
