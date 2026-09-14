<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
    // POST /api/sales-orders
    // Body: { customer_id, sale_date, items: [{ animal_id, sale_weight_kg, price_per_kg }] }
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
            $total = collect($validated['items'])
                ->sum(fn ($i) => $i['sale_weight_kg'] * $i['price_per_kg']);

            $order = SalesOrder::create([
                'so_number' => 'SO-' . now()->format('Ymd') . '-' . strtoupper(uniqid()),
                'customer_id' => $validated['customer_id'],
                'sale_date' => $validated['sale_date'],
                'status' => 'completed',
                'total_amount' => $total,
            ]);

            foreach ($validated['items'] as $item) {
                $lineTotal = $item['sale_weight_kg'] * $item['price_per_kg'];

                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'animal_id' => $item['animal_id'],
                    'sale_weight_kg' => $item['sale_weight_kg'],
                    'price_per_kg' => $item['price_per_kg'],
                    'line_total' => $lineTotal,
                ]);

                // Settlement: mark the animal sold and record its exit weight/date.
                // This is what removes it from "on feed" counts and feed-cost accrual.
                Animal::where('id', $item['animal_id'])->update([
                    'status' => 'sold',
                    'exit_date' => $validated['sale_date'],
                    'exit_weight_kg' => $item['sale_weight_kg'],
                ]);
            }

            // If every animal in a batch is now sold/dead, close the batch
            $batchIds = Animal::whereIn('id', collect($validated['items'])->pluck('animal_id'))
                ->pluck('batch_id')
                ->unique();

            foreach ($batchIds as $batchId) {
                $stillOnFeed = Animal::where('batch_id', $batchId)->where('status', 'on_feed')->exists();
                $anySold = Animal::where('batch_id', $batchId)->where('status', 'sold')->exists();

                if (! $stillOnFeed && $anySold) {
                    \App\Models\Batch::where('id', $batchId)->update([
                        'status' => 'closed',
                        'actual_end_date' => $validated['sale_date'],
                    ]);
                } elseif ($anySold) {
                    \App\Models\Batch::where('id', $batchId)->update(['status' => 'partially_sold']);
                }
            }

            return $order;
        });

        return response()->json($order->load('items'), 201);
    }

    // GET /api/sales-orders/{salesOrder}
    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load(['customer', 'items.animal']);

        return response()->json(array_merge($salesOrder->toArray(), [
            'amount_paid' => $salesOrder->amountPaid(),
            'balance_due' => $salesOrder->balanceDue(),
        ]));
    }
}
