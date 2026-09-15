<?php

namespace Tests\Feature\Web;

use App\Models\Pen;
use App\Models\Species;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class BatchTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_admin_can_create_a_batch_through_the_web_form(): void
    {
        $species = Species::factory()->create();
        $pen = Pen::factory()->create();

        $response = $this->actingAs($this->adminUser())->post('/batches', [
            'batch_code' => 'WEB-B-1',
            'species_id' => $species->id,
            'pen_id' => $pen->id,
            'start_date' => now()->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('batches', ['batch_code' => 'WEB-B-1']);
    }

    public function test_a_vet_gets_a_403_page_trying_to_create_a_batch(): void
    {
        $species = Species::factory()->create();

        $response = $this->actingAs($this->userWithRole('Vet'))->post('/batches', [
            'batch_code' => 'WEB-B-2',
            'species_id' => $species->id,
            'start_date' => now()->toDateString(),
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('batches', ['batch_code' => 'WEB-B-2']);
    }

    public function test_the_batch_create_form_is_still_visible_to_a_vet(): void
    {
        $response = $this->actingAs($this->userWithRole('Vet'))->get('/batches/create');

        $response->assertOk();
    }
}
