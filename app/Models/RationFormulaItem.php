<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RationFormulaItem extends Model
{
    protected $fillable = ['ration_formula_id', 'feed_item_id', 'quantity_kg_per_head'];

    public function rationFormula()
    {
        return $this->belongsTo(RationFormula::class);
    }

    public function feedItem()
    {
        return $this->belongsTo(FeedItem::class);
    }
}
