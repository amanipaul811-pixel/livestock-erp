<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimalMovement extends Model
{
    use HasFactory;

    protected $fillable = ['animal_id', 'from_pen_id', 'to_pen_id', 'move_date', 'reason'];

    protected $casts = ['move_date' => 'date'];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function fromPen()
    {
        return $this->belongsTo(Pen::class, 'from_pen_id');
    }

    public function toPen()
    {
        return $this->belongsTo(Pen::class, 'to_pen_id');
    }
}
