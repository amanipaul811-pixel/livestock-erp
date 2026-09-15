<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'reference_type' => 'sales_order',
            'reference_id' => 1,
            'payment_date' => now(),
            'amount' => 100,
            'method' => 'cash',
        ];
    }
}
