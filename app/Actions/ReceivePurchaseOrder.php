<?php

namespace App\Actions;

use App\Models\Expense;
use App\Models\FeedStockMovement;
use App\Models\PurchaseOrder;
use App\Models\User;

// Fires the downstream effect a "received" PO should have always had -- feed
// stock actually arriving, or an overhead expense actually landing -- instead
// of the PO being a record that nothing else in the app reacts to. Shared by
// the Web and Api controllers so the two can't drift apart on this logic.
class ReceivePurchaseOrder
{
    public function handle(PurchaseOrder $purchaseOrder, ?User $recordedBy): void
    {
        if ($purchaseOrder->order_type === 'feed' && $purchaseOrder->feed_item_id) {
            FeedStockMovement::create([
                'feed_item_id' => $purchaseOrder->feed_item_id,
                'type' => 'in',
                'quantity_kg' => $purchaseOrder->quantity_kg,
                'reason' => "Received {$purchaseOrder->po_number}",
                'recorded_by' => $recordedBy?->id,
                'occurred_at' => now(),
            ]);

            return;
        }

        if (in_array($purchaseOrder->order_type, ['medicine', 'other'], true)) {
            Expense::create([
                'batch_id' => null,
                'category' => 'other',
                'expense_date' => now(),
                'amount' => $purchaseOrder->total_amount,
                'description' => "Received {$purchaseOrder->po_number} ({$purchaseOrder->order_type}) from {$purchaseOrder->supplier->name}",
                'recorded_by' => $recordedBy?->id,
            ]);
        }

        // order_type=animal is intentionally left manual: receiving a batch of
        // animals still means walking them in individually via Batch > Add
        // Animal (tag IDs, weights, pens) -- there's no way to auto-derive
        // that from a single PO total.
    }
}
