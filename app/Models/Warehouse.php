<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = ['name', 'location', 'type'];

    public function feedItems()
    {
        return $this->hasMany(FeedItem::class);
    }
}
