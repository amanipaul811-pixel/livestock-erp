<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedLog extends Model
{
    use HasFactory;

    protected $fillable = ['batch_id', 'feed_item_id', 'feed_date', 'quantity_kg', 'total_cost', 'recorded_by'];

    protected $casts = ['feed_date' => 'date'];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function feedItem()
    {
        return $this->belongsTo(FeedItem::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
