<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RationFormula extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'species_id', 'stage'];

    public function species()
    {
        return $this->belongsTo(Species::class);
    }

    public function items()
    {
        return $this->hasMany(RationFormulaItem::class);
    }

    // Daily feed cost per head for this ration
    public function dailyCostPerHead(): float
    {
        return $this->items->sum(function (RationFormulaItem $item) {
            return $item->quantity_kg_per_head * $item->feedItem->cost_per_unit;
        });
    }
}
