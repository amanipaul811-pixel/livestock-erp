<?php

namespace Tests\Feature\Web;

use App\Models\Animal;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\HealthRecord;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_it_shows_the_current_month_by_default(): void
    {
        $response = $this->actingAs($this->adminUser())->get('/calendar');

        $response->assertOk();
        $response->assertViewHas('month', fn ($month) => $month->isSameMonth(now()) && $month->day === 1);
    }

    public function test_a_sale_in_the_month_appears_as_an_event(): void
    {
        $customer = Customer::factory()->create(['name' => 'Acme Meats']);
        $animal = Animal::factory()->create();
        $so = SalesOrder::factory()->create(['customer_id' => $customer->id, 'sale_date' => now()->startOfMonth()->addDays(4)]);
        SalesOrderItem::factory()->create(['sales_order_id' => $so->id, 'animal_id' => $animal->id]);

        $response = $this->actingAs($this->adminUser())->get('/calendar');

        $response->assertOk();
        $response->assertSee('Acme Meats');
    }

    public function test_a_health_record_outside_the_month_does_not_appear(): void
    {
        $animal = Animal::factory()->create(['tag_id' => 'OUT-0001']);
        HealthRecord::factory()->create(['animal_id' => $animal->id, 'record_date' => now()->addMonths(2)]);

        $response = $this->actingAs($this->adminUser())->get('/calendar');

        $response->assertOk();
        $response->assertDontSee('OUT-0001');
    }

    public function test_a_batch_expected_to_finish_this_month_shows_up(): void
    {
        Batch::factory()->create(['batch_code' => 'B-EXPECT-01', 'expected_end_date' => now()->startOfMonth()->addDays(10)]);

        $response = $this->actingAs($this->adminUser())->get('/calendar');

        $response->assertOk();
        $response->assertSee('B-EXPECT-01');
    }

    public function test_navigating_to_next_month_changes_the_displayed_month(): void
    {
        $nextMonth = now()->addMonth()->format('Y-m');

        $response = $this->actingAs($this->adminUser())->get("/calendar?month={$nextMonth}");

        $response->assertOk();
        $response->assertViewHas('month', fn ($month) => $month->format('Y-m') === $nextMonth);
    }

    public function test_an_invalid_month_falls_back_to_the_current_month(): void
    {
        $response = $this->actingAs($this->adminUser())->get('/calendar?month=not-a-month');

        $response->assertOk();
        $response->assertViewHas('month', fn ($month) => $month->isSameMonth(now()));
    }
}
