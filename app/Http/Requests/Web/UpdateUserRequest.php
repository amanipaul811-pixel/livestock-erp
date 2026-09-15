<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:150',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'phone' => 'nullable|string|max:30',
            'password' => ['nullable', Password::min(8)->mixedCase()->numbers()],
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
        ];
    }
}
