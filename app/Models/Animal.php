<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    use HasFactory;

    protected $fillable = [
        'tag_id', 'batch_id', 'species_id', 'breed', 'sex',
        'estimated_age_months', 'entry_date', 'entry_weight_kg',
        'purchase_price', 'supplier_id', 'current_pen_id', 'status',
        'exit_date', 'exit_weight_kg',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'exit_date' => 'date',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function species()
    {
        return $this->belongsTo(Species::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function currentPen()
    {
        return $this->belongsTo(Pen::class, 'current_pen_id');
    }

    public function weighIns()
    {
        return $this->hasMany(WeighIn::class)->orderBy('weigh_date');
    }

    public function healthRecords()
    {
        return $this->hasMany(HealthRecord::class);
    }

    public function movements()
    {
        return $this->hasMany(AnimalMovement::class);
    }

    // --- Growth calculations ---

    // Latest recorded weight, falling back to entry weight if never weighed
    public function latestWeightKg(): float
    {
        // weighIns() sorts ascending for chronological display elsewhere, so
        // reorder() clears that before sorting descending here -- appending
        // ->latest() on top of an existing orderBy on the same column is a
        // no-op in most SQL engines and would silently return the oldest row.
        $latest = $this->weighIns()->reorder('weigh_date', 'desc')->first();

        return $latest ? (float) $latest->weight_kg : (float) $this->entry_weight_kg;
    }

    public function currentWeightGainKg(): float
    {
        return $this->latestWeightKg() - (float) $this->entry_weight_kg;
    }

    // Average Daily Gain since entry
    public function averageDailyGainKg(): float
    {
        // Cast to int: Carbon 3's diffInDays() returns a fractional day count
        // (time-of-day included) rather than the truncated whole-day count
        // this calculation assumes.
        $days = max(1, (int) $this->entry_date->diffInDays(now()));

        return round($this->currentWeightGainKg() / $days, 2);
    }

    public function isReadyToSell(): bool
    {
        $target = $this->species->target_exit_weight_kg;

        return $target !== null && $this->latestWeightKg() >= $target;
    }
}
