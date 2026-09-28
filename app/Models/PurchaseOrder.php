<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = ['po_number', 'supplier_id', 'order_type', 'feed_item_id', 'quantity_kg', 'species_id', 'quantity', 'order_date', 'total_amount', 'status'];

    protected $casts = ['order_date' => 'date'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function feedItem()
    {
        return $this->belongsTo(FeedItem::class);
    }

    public function species()
    {
        return $this->belongsTo(Species::class);
    }

    public function animals()
    {
        return $this->hasMany(Animal::class);
    }

    public function animalsReceivedCount(): int
    {
        return $this->animals()->count();
    }

    public function animalsRemaining(): int
    {
        return max(0, (int) $this->quantity - $this->animalsReceivedCount());
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'reference_id')
            ->where('reference_type', 'purchase_order');
    }

    public function amountPaid(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function balanceDue(): float
    {
        return (float) $this->total_amount - $this->amountPaid();
    }
}
