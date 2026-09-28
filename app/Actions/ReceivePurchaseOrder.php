<?php

namespace App\Actions;

use App\Models\Batch;
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

        // order_type=animal: each animal PO is its own buying event, so it
        // gets its own dedicated batch -- never an existing one, even one for
        // the same species bought from the same supplier hours earlier.
        // Walking each animal in individually (tag, weight, pen) still stays
        // manual; there's no way to auto-derive that from a single PO total.
        if ($purchaseOrder->order_type === 'animal') {
            $this->ensureBatch($purchaseOrder, $recordedBy);
        }
    }

    // Idempotent: safe to call again for a PO that already has its batch
    // (returns the existing one), which also lets it double as a one-time
    // backfill for orders received before this batch-per-order link existed.
    public function ensureBatch(PurchaseOrder $purchaseOrder, ?User $recordedBy): ?Batch
    {
        if ($purchaseOrder->batch_id) {
            return $purchaseOrder->batch;
        }

        if ($purchaseOrder->order_type !== 'animal' || ! $purchaseOrder->species_id) {
            return null;
        }

        $batch = Batch::create([
            'batch_code' => Batch::nextBatchCode(),
            'species_id' => $purchaseOrder->species_id,
            'start_date' => now(),
            'status' => 'active',
            'created_by' => $recordedBy?->id,
        ]);

        $purchaseOrder->update(['batch_id' => $batch->id]);

        return $batch;
    }
}
