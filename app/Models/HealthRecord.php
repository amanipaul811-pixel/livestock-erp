<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'animal_id', 'record_type', 'record_date', 'description',
        'medicine_used', 'cost', 'performed_by', 'cause_of_death',
    ];

    protected $casts = ['record_date' => 'date'];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
