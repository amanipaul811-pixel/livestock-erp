<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = ['po_number', 'supplier_id', 'order_type', 'order_date', 'total_amount', 'status'];

    protected $casts = ['order_date' => 'date'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
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
