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

    public function test_a_vet_is_turned_away_from_the_create_form_itself_not_just_the_submit(): void
    {
        // Previously the create form was reachable and only rejected on submit
        // (a wasted-effort UX gap); the GET route is now gated the same as
        // the POST, so a role without the permission never even sees it.
        $response = $this->actingAs($this->userWithRole('Vet'))->get('/batches/create');

        $response->assertForbidden();
    }

    public function test_the_new_batch_link_is_hidden_from_a_vet_on_the_dashboard(): void
    {
        $response = $this->actingAs($this->userWithRole('Vet'))->get('/dashboard');

        $response->assertOk()->assertDontSee('New Batch');
    }

    public function test_the_new_batch_link_is_visible_to_an_admin_on_the_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser())->get('/dashboard');

        $response->assertOk()->assertSee('New Batch');
    }
}
