<?php

namespace Database\Factories;

use App\Models\Batch;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'batch_id' => Batch::factory(),
            'category' => 'other',
            'expense_date' => now(),
            'amount' => 10,
        ];
    }
}
