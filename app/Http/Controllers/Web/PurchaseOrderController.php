<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;

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

    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,received,cancelled',
        ]);

        $purchaseOrder->update($validated);

        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('status', 'Status updated.');
    }
}
