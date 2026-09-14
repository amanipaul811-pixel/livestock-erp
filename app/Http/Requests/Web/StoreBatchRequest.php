<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'batch_code' => 'required|string|unique:batches,batch_code',
            'species_id' => 'required|exists:species,id',
            'pen_id' => 'nullable|exists:pens,id',
            'start_date' => 'required|date',
            'expected_end_date' => 'nullable|date|after:start_date',
            'notes' => 'nullable|string',
        ];
    }
}
