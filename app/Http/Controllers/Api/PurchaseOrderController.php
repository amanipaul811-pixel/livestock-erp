<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePurchaseOrderRequest;
use App\Http\Requests\Api\UpdatePurchaseOrderRequest;
use App\Models\PurchaseOrder;

class PurchaseOrderController extends Controller
{
    // GET /api/purchase-orders
    public function index()
    {
        return response()->json(PurchaseOrder::with('supplier')->latest('order_date')->get());
    }

    // POST /api/purchase-orders
    public function store(StorePurchaseOrderRequest $request)
    {
        $validated = $request->validated();
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
    public function update(UpdatePurchaseOrderRequest $request, PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->update($request->validated());

        return response()->json($purchaseOrder);
    }
}
