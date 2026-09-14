<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeighIn extends Model
{
    protected $fillable = ['animal_id', 'weigh_date', 'weight_kg', 'recorded_by', 'notes'];

    protected $casts = ['weigh_date' => 'date'];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
