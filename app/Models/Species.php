<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Species extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'default_cycle_days', 'target_adg_kg',
        'target_entry_weight_kg', 'target_exit_weight_kg', 'default_price_per_kg',
    ];

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function animals()
    {
        return $this->hasMany(Animal::class);
    }

    public function rationFormulas()
    {
        return $this->hasMany(RationFormula::class);
    }
}
