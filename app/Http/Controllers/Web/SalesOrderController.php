<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
    public function create()
    {
        $onFeed = Animal::where('status', 'on_feed')->with(['species', 'batch'])->get();

        return view('sales-orders.create', [
            'customers' => Customer::orderBy('name')->get(),
            'animals' => $onFeed,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.animal_id' => 'required|exists:animals,id',
            'items.*.sale_weight_kg' => 'required|numeric|min:0',
            'items.*.price_per_kg' => 'required|numeric|min:0',
        ]);

        $order = DB::transaction(function () use ($validated) {
            $total = collect($validated['items'])->sum(fn ($i) => $i['sale_weight_kg'] * $i['price_per_kg']);

            $order = SalesOrder::create([
                'so_number' => 'SO-'.now()->format('Ymd').'-'.strtoupper(uniqid()),
                'customer_id' => $validated['customer_id'],
                'sale_date' => $validated['sale_date'],
                'status' => 'completed',
                'total_amount' => $total,
            ]);

            foreach ($validated['items'] as $item) {
                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'animal_id' => $item['animal_id'],
                    'sale_weight_kg' => $item['sale_weight_kg'],
                    'price_per_kg' => $item['price_per_kg'],
                    'line_total' => $item['sale_weight_kg'] * $item['price_per_kg'],
                ]);

                Animal::where('id', $item['animal_id'])->update([
                    'status' => 'sold',
                    'exit_date' => $validated['sale_date'],
                    'exit_weight_kg' => $item['sale_weight_kg'],
                ]);
            }

            $batchIds = Animal::whereIn('id', collect($validated['items'])->pluck('animal_id'))
                ->pluck('batch_id')->unique();

            foreach ($batchIds as $batchId) {
                $stillOnFeed = Animal::where('batch_id', $batchId)->where('status', 'on_feed')->exists();
                $anySold = Animal::where('batch_id', $batchId)->where('status', 'sold')->exists();

                if (! $stillOnFeed && $anySold) {
                    Batch::where('id', $batchId)->update(['status' => 'closed', 'actual_end_date' => $validated['sale_date']]);
                } elseif ($anySold) {
                    Batch::where('id', $batchId)->update(['status' => 'partially_sold']);
                }
            }

            return $order;
        });

        return redirect()->route('sales-orders.show', $order)->with('status', "Sale {$order->so_number} recorded.");
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load(['customer', 'items.animal']);

        return view('sales-orders.show', [
            'order' => $salesOrder,
            'payments' => $salesOrder->payments()->orderBy('payment_date')->get(),
            'amountPaid' => $salesOrder->amountPaid(),
            'balanceDue' => $salesOrder->balanceDue(),
        ]);
    }
}
