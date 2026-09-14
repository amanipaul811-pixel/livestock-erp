<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePurchaseOrderPaymentRequest;
use App\Http\Requests\Api\StoreSalesOrderPaymentRequest;
use App\Models\Payment;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;

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
    public function store(StoreSalesOrderPaymentRequest $request, SalesOrder $salesOrder)
    {
        $validated = $request->validated();
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
    public function storeForPurchaseOrder(StorePurchaseOrderPaymentRequest $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validated();
        $validated['reference_type'] = 'purchase_order';
        $validated['reference_id'] = $purchaseOrder->id;

        $payment = Payment::create($validated);

        return response()->json($payment, 201);
    }
}
