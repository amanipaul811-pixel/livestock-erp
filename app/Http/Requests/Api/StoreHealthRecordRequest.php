<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreHealthRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'record_type' => 'required|in:vaccination,deworming,treatment,checkup,death',
            'record_date' => 'required|date',
            'description' => 'nullable|string',
            'medicine_used' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'cause_of_death' => 'nullable|string|required_if:record_type,death',
        ];
    }
}
