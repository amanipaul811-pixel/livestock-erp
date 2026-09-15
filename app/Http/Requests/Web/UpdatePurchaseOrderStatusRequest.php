<?php

namespace App\Http\Requests\Web;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'in:pending,received,cancelled',
                // received/cancelled are terminal -- the web UI only shows
                // Mark Received/Cancel while pending, but that's client-side
                // only; enforce it here too.
                function (string $attribute, mixed $value, Closure $fail) {
                    $current = $this->route('purchaseOrder')->status;

                    if (in_array($current, ['received', 'cancelled']) && $value !== $current) {
                        $fail("This purchase order is already {$current} and can't be changed.");
                    }
                },
            ],
        ];
    }
}
