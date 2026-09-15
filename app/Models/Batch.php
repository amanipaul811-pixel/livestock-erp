<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_code', 'species_id', 'pen_id', 'start_date',
        'expected_end_date', 'actual_end_date', 'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expected_end_date' => 'date',
        'actual_end_date' => 'date',
    ];

    public function species()
    {
        return $this->belongsTo(Species::class);
    }

    public function pen()
    {
        return $this->belongsTo(Pen::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function animals()
    {
        return $this->hasMany(Animal::class);
    }

    public function feedLogs()
    {
        return $this->hasMany(FeedLog::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // --- Batch-level KPIs, computed from underlying data (not stored) ---

    public function daysOnFeed(): int
    {
        // Cast explicitly: Carbon 3's diffInDays() returns a fractional day
        // count, not the truncated whole-day count this relies on. The `:
        // int` return type happens to coerce it, but do it explicitly so the
        // truncation isn't hidden behind an implicit type-juggling rule.
        return (int) $this->start_date->diffInDays(now());
    }

    public function totalFeedCost(): float
    {
        return (float) $this->feedLogs()->sum('total_cost');
    }

    public function totalFeedKg(): float
    {
        return (float) $this->feedLogs()->sum('quantity_kg');
    }

    public function totalPurchaseCost(): float
    {
        return (float) $this->animals()->sum('purchase_price');
    }

    public function totalHealthCost(): float
    {
        return (float) HealthRecord::whereIn('animal_id', $this->animals()->pluck('id'))->sum('cost');
    }

    public function totalOtherExpenses(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    public function totalSalesRevenue(): float
    {
        return (float) SalesOrderItem::whereIn('animal_id', $this->animals()->pluck('id'))->sum('line_total');
    }

    // Feed Conversion Ratio = total feed kg consumed / total live weight gained
    public function feedConversionRatio(): ?float
    {
        $gainKg = $this->animals->sum(function (Animal $a) {
            return $a->currentWeightGainKg();
        });

        if ($gainKg <= 0) {
            return null;
        }

        return round($this->totalFeedKg() / $gainKg, 2);
    }

    public function netProfit(): float
    {
        return $this->totalSalesRevenue()
            - $this->totalPurchaseCost()
            - $this->totalFeedCost()
            - $this->totalHealthCost()
            - $this->totalOtherExpenses();
    }
}
