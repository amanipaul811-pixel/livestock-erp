<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pen extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'capacity', 'stage', 'is_active'];

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function animals()
    {
        return $this->hasMany(Animal::class, 'current_pen_id');
    }

    // Head count currently in this pen, vs. capacity
    public function occupancy(): int
    {
        return $this->animals()->where('status', 'on_feed')->count();
    }
}
