<?php

namespace Tests\Feature\Web;

use App\Models\Animal;
use App\Models\Batch;
use App\Models\HealthRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class HealthReportTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_it_lists_health_records_in_range_and_totals_cost(): void
    {
        $animal = Animal::factory()->create(['tag_id' => 'CTL-7001']);
        HealthRecord::factory()->create(['animal_id' => $animal->id, 'record_type' => 'vaccination', 'record_date' => now(), 'cost' => 12]);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get('/reports/health');

        $response->assertOk();
        $response->assertSee('CTL-7001');
        $response->assertViewHas('totalCost', 12.0);
    }

    public function test_filtering_by_batch_shows_only_that_batchs_animals(): void
    {
        $batchA = Batch::factory()->create();
        $batchB = Batch::factory()->create();
        $animalA = Animal::factory()->create(['batch_id' => $batchA->id, 'tag_id' => 'A-100']);
        $animalB = Animal::factory()->create(['batch_id' => $batchB->id, 'tag_id' => 'B-100']);
        HealthRecord::factory()->create(['animal_id' => $animalA->id, 'record_date' => now()]);
        HealthRecord::factory()->create(['animal_id' => $animalB->id, 'record_date' => now()]);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get("/reports/health?batch_id={$batchA->id}");

        $response->assertOk();
        $response->assertSee('A-100');
        $response->assertDontSee('B-100');
    }

    public function test_filtering_by_record_type(): void
    {
        $animal = Animal::factory()->create();
        HealthRecord::factory()->create(['animal_id' => $animal->id, 'record_type' => 'death', 'record_date' => now()]);
        HealthRecord::factory()->create(['animal_id' => $animal->id, 'record_type' => 'checkup', 'record_date' => now()]);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get('/reports/health?record_type=death');

        $response->assertOk();
        $response->assertViewHas('rows', fn ($rows) => $rows->count() === 1);
    }

    public function test_a_feeder_cannot_view_the_health_report(): void
    {
        $response = $this->actingAs($this->userWithRole('Feeder'))->get('/reports/health');

        $response->assertForbidden();
    }

    public function test_pdf_export_downloads_a_pdf(): void
    {
        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get('/reports/health/export/pdf');

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }
}
