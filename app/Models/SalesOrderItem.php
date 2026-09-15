<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['sales_order_id', 'animal_id', 'sale_weight_kg', 'price_per_kg', 'line_total'];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }
}
