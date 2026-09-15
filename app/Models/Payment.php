<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['reference_type', 'reference_id', 'payment_date', 'amount', 'method', 'notes'];

    protected $casts = ['payment_date' => 'date'];

    // Polymorphic-style helper: resolve the actual SalesOrder or PurchaseOrder
    public function reference()
    {
        return $this->reference_type === 'sales_order'
            ? SalesOrder::find($this->reference_id)
            : PurchaseOrder::find($this->reference_id);
    }
}
