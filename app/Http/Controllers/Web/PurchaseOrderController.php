<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StorePurchaseOrderRequest;
use App\Http\Requests\Web\UpdatePurchaseOrderStatusRequest;
use App\Models\PurchaseOrder;
use App\Models\Supplier;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        return view('purchase-orders.index', [
            'orders' => PurchaseOrder::with('supplier')->latest('order_date')->get(),
        ]);
    }

    public function create()
    {
        return view('purchase-orders.create', [
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }

    public function store(StorePurchaseOrderRequest $request)
    {
        $validated = $request->validated();
        $validated['po_number'] = 'PO-'.now()->format('Ymd').'-'.strtoupper(uniqid());
        $validated['status'] = 'pending';

        $order = PurchaseOrder::create($validated);

        return redirect()->route('purchase-orders.show', $order)->with('status', "Purchase order {$order->po_number} created.");
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('supplier');

        return view('purchase-orders.show', [
            'order' => $purchaseOrder,
            'payments' => $purchaseOrder->payments()->orderBy('payment_date')->get(),
            'amountPaid' => $purchaseOrder->amountPaid(),
            'balanceDue' => $purchaseOrder->balanceDue(),
        ]);
    }

    public function updateStatus(UpdatePurchaseOrderStatusRequest $request, PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->update($request->validated());

        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('status', 'Status updated.');
    }
}
