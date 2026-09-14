<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pen_id' => 'nullable|exists:pens,id',
            'expected_end_date' => 'nullable|date',
            'actual_end_date' => 'nullable|date',
            'status' => 'nullable|in:active,partially_sold,closed',
            'notes' => 'nullable|string',
        ];
    }
}
