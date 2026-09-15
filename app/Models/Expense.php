<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = ['batch_id', 'category', 'expense_date', 'amount', 'description', 'recorded_by'];

    protected $casts = ['expense_date' => 'date'];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
