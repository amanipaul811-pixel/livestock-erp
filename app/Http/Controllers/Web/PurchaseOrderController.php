<?php

namespace App\Http\Controllers\Web;

use App\Actions\ReceivePurchaseOrder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StorePurchaseOrderRequest;
use App\Http\Requests\Web\UpdatePurchaseOrderStatusRequest;
use App\Models\Batch;
use App\Models\FeedItem;
use App\Models\PurchaseOrder;
use App\Models\Species;
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
            'feedItems' => FeedItem::orderBy('name')->get(),
            'speciesList' => Species::orderBy('name')->get(),
        ]);
    }

    public function store(StorePurchaseOrderRequest $request)
    {
        $validated = $request->validated();
        $validated['po_number'] = PurchaseOrder::nextPoNumber();
        $validated['status'] = 'pending';

        $order = PurchaseOrder::create($validated);

        return redirect()->route('purchase-orders.show', $order)->with('status', "Purchase order {$order->po_number} created.");
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'species', 'animals.currentPen']);

        return view('purchase-orders.show', [
            'order' => $purchaseOrder,
            'payments' => $purchaseOrder->payments()->orderBy('payment_date')->get(),
            'amountPaid' => $purchaseOrder->amountPaid(),
            'balanceDue' => $purchaseOrder->balanceDue(),
            'eligibleBatches' => $purchaseOrder->order_type === 'animal' && $purchaseOrder->species_id
                ? Batch::where('species_id', $purchaseOrder->species_id)->where('status', 'active')->orderBy('batch_code')->get()
                : collect(),
        ]);
    }

    public function updateStatus(UpdatePurchaseOrderStatusRequest $request, PurchaseOrder $purchaseOrder, ReceivePurchaseOrder $receivePurchaseOrder)
    {
        $wasReceived = $purchaseOrder->status === 'received';

        $purchaseOrder->update($request->validated());

        if (! $wasReceived && $purchaseOrder->status === 'received') {
            $receivePurchaseOrder->handle($purchaseOrder, $request->user());
        }

        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('status', 'Status updated.');
    }
}
