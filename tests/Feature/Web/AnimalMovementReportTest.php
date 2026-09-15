<?php

namespace Tests\Feature\Web;

use App\Models\Animal;
use App\Models\AnimalMovement;
use App\Models\Batch;
use App\Models\Pen;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class AnimalMovementReportTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_it_lists_movements_in_range(): void
    {
        $animal = Animal::factory()->create(['tag_id' => 'CTL-9001']);
        $pen = Pen::factory()->create();
        AnimalMovement::factory()->create(['animal_id' => $animal->id, 'to_pen_id' => $pen->id, 'move_date' => now()]);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get('/reports/movements');

        $response->assertOk();
        $response->assertSee('CTL-9001');
    }

    public function test_filtering_by_tag_excludes_other_animals(): void
    {
        $match = Animal::factory()->create(['tag_id' => 'CTL-1234']);
        $other = Animal::factory()->create(['tag_id' => 'GT-5678']);
        AnimalMovement::factory()->create(['animal_id' => $match->id, 'move_date' => now()]);
        AnimalMovement::factory()->create(['animal_id' => $other->id, 'move_date' => now()]);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get('/reports/movements?tag=CTL');

        $response->assertOk();
        $response->assertSee('CTL-1234');
        $response->assertDontSee('GT-5678');
    }

    public function test_filtering_by_batch_excludes_other_batches(): void
    {
        $batchA = Batch::factory()->create();
        $batchB = Batch::factory()->create();
        $animalA = Animal::factory()->create(['batch_id' => $batchA->id, 'tag_id' => 'A-001']);
        $animalB = Animal::factory()->create(['batch_id' => $batchB->id, 'tag_id' => 'B-001']);
        AnimalMovement::factory()->create(['animal_id' => $animalA->id, 'move_date' => now()]);
        AnimalMovement::factory()->create(['animal_id' => $animalB->id, 'move_date' => now()]);

        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get("/reports/movements?batch_id={$batchA->id}");

        $response->assertOk();
        $response->assertSee('A-001');
        $response->assertDontSee('B-001');
    }

    public function test_a_vet_cannot_view_the_movements_report(): void
    {
        $response = $this->actingAs($this->userWithRole('Vet'))->get('/reports/movements');

        $response->assertForbidden();
    }

    public function test_pdf_export_downloads_a_pdf(): void
    {
        $response = $this->actingAs($this->userWithRole('Farm Manager'))->get('/reports/movements/export/pdf');

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }
}
