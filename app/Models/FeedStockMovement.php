<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedStockMovement extends Model
{
    use HasFactory;

    protected $fillable = ['feed_item_id', 'type', 'quantity_kg', 'reason', 'recorded_by', 'occurred_at'];

    protected $casts = ['occurred_at' => 'datetime'];

    public function feedItem()
    {
        return $this->belongsTo(FeedItem::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
