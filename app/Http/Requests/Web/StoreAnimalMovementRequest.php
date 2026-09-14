<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnimalMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'to_pen_id' => 'required|exists:pens,id',
            'move_date' => 'required|date',
            'reason' => 'nullable|string|max:150',
        ];
    }
}
