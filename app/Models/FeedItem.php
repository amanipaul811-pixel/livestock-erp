<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedItem extends Model
{
    use HasFactory;

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

    public function stockMovements()
    {
        return $this->hasMany(FeedStockMovement::class);
    }

    public function currentStock(): float
    {
        $in = (float) $this->stockMovements()->where('type', 'in')->sum('quantity_kg');
        $out = (float) $this->stockMovements()->where('type', 'out')->sum('quantity_kg');
        $adjustment = (float) $this->stockMovements()->where('type', 'adjustment')->sum('quantity_kg');

        return $in - $out + $adjustment;
    }

    public function isLowStock(): bool
    {
        return $this->reorder_level > 0 && $this->currentStock() <= $this->reorder_level;
    }
}
