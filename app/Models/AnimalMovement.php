<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimalMovement extends Model
{
    use HasFactory;

    protected $fillable = ['animal_id', 'from_pen_id', 'to_pen_id', 'weight_kg_at_move', 'move_date', 'reason'];

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

    // When this animal's stay in the from-pen actually started: the date of
    // whichever movement most recently brought it there, or its entry_date
    // if it has been there since first being intaken (no earlier movement).
    public function stayStartDate()
    {
        $previous = static::where('animal_id', $this->animal_id)
            ->where('move_date', '<', $this->move_date)
            ->orderByDesc('move_date')
            ->orderByDesc('id')
            ->first();

        return $previous ? $previous->move_date : $this->animal->entry_date;
    }

    // Health records logged for this animal while it was in the from-pen --
    // computed from the stay's date window rather than duplicated onto the
    // movement, since health records here are never edited after creation.
    public function healthRecordsDuringStay()
    {
        return HealthRecord::where('animal_id', $this->animal_id)
            ->whereBetween('record_date', [$this->stayStartDate(), $this->move_date])
            ->orderBy('record_date')
            ->get();
    }

    // Weigh-ins logged for this animal while it was in the from-pen.
    public function weighInsDuringStay()
    {
        return WeighIn::where('animal_id', $this->animal_id)
            ->whereBetween('weigh_date', [$this->stayStartDate(), $this->move_date])
            ->orderBy('weigh_date')
            ->get();
    }

    public function daysInPen(): int
    {
        return (int) $this->stayStartDate()->diffInDays($this->move_date);
    }
}
