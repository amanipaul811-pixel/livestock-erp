<?php

namespace App\Models;

use App\Concerns\GeneratesSequentialCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasFactory, GeneratesSequentialCode;

    protected $fillable = ['so_number', 'customer_id', 'sale_date', 'status', 'total_amount'];

    protected $casts = ['sale_date' => 'date'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'reference_id')
            ->where('reference_type', 'sales_order');
    }

    public function amountPaid(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function balanceDue(): float
    {
        return (float) $this->total_amount - $this->amountPaid();
    }

    public static function nextSoNumber(): string
    {
        return static::nextCodeWithPrefix('so_number', 'SO-'.now()->year.'-', 4);
    }
}
