<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    // GET /api/purchase-orders
    public function index()
    {
        return response()->json(PurchaseOrder::with('supplier')->latest('order_date')->get());
    }

    // POST /api/purchase-orders
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_type' => 'required|in:animal,feed,medicine,other',
            'order_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $validated['po_number'] = 'PO-'.now()->format('Ymd').'-'.strtoupper(uniqid());
        $validated['status'] = 'pending';

        $order = PurchaseOrder::create($validated);

        return response()->json($order, 201);
    }

    // GET /api/purchase-orders/{purchaseOrder}
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('supplier');

        return response()->json(array_merge($purchaseOrder->toArray(), [
            'amount_paid' => $purchaseOrder->amountPaid(),
            'balance_due' => $purchaseOrder->balanceDue(),
        ]));
    }

    // PATCH /api/purchase-orders/{purchaseOrder}
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,received,cancelled',
        ]);

        $purchaseOrder->update($validated);

        return response()->json($purchaseOrder);
    }
}
