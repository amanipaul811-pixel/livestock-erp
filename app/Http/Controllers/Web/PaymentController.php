<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StorePurchaseOrderPaymentRequest;
use App\Http\Requests\Web\StoreSalesOrderPaymentRequest;
use App\Models\Payment;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;

class PaymentController extends Controller
{
    public function store(StoreSalesOrderPaymentRequest $request, SalesOrder $salesOrder)
    {
        $validated = $request->validated();
        $validated['reference_type'] = 'sales_order';
        $validated['reference_id'] = $salesOrder->id;

        Payment::create($validated);

        return redirect()->route('sales-orders.show', $salesOrder)->with('status', 'Payment recorded.');
    }

    public function storeForPurchaseOrder(StorePurchaseOrderPaymentRequest $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validated();
        $validated['reference_type'] = 'purchase_order';
        $validated['reference_id'] = $purchaseOrder->id;

        Payment::create($validated);

        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('status', 'Payment recorded.');
    }
}
