<?php

namespace Tests\Feature\Api;

use App\Models\Batch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_farm_manager_can_log_an_expense(): void
    {
        Sanctum::actingAs($this->userWithRole('Farm Manager'));
        $batch = Batch::factory()->create();

        $response = $this->postJson("/api/batches/{$batch->id}/expenses", [
            'category' => 'transport',
            'expense_date' => now()->toDateString(),
            'amount' => 40,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('expenses', ['batch_id' => $batch->id, 'amount' => 40]);
    }

    public function test_expense_reduces_batch_net_profit(): void
    {
        Sanctum::actingAs($this->userWithRole('Farm Manager'));
        $batch = Batch::factory()->create();

        $this->postJson("/api/batches/{$batch->id}/expenses", [
            'category' => 'labor',
            'expense_date' => now()->toDateString(),
            'amount' => 25,
        ])->assertCreated();

        $response = $this->getJson("/api/batches/{$batch->id}");

        $response->assertOk()->assertJsonPath('net_profit', -25);
    }
}
