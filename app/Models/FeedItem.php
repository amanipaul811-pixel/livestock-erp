<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedItem extends Model
{
    protected $fillable = ['name', 'unit', 'cost_per_unit', 'warehouse_id', 'reorder_level'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function feedLogs()
    {
        return $this->hasMany(FeedLog::class);
    }

    public function rationFormulaItems()
    {
        return $this->hasMany(RationFormulaItem::class);
    }
}
