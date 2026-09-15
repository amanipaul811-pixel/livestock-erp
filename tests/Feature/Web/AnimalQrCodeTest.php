<?php

namespace Tests\Feature\Web;

use App\Models\Animal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class AnimalQrCodeTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_the_qr_code_endpoint_returns_an_svg(): void
    {
        $animal = Animal::factory()->create();

        $response = $this->actingAs($this->adminUser())->get("/animals/{$animal->id}/qr-code");

        $response->assertOk();
        $this->assertStringContainsString('image/svg+xml', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('<svg', $response->getContent());
    }

    public function test_the_printable_tag_page_shows_the_animal_details(): void
    {
        $animal = Animal::factory()->create(['tag_id' => 'TAG-0042']);

        $response = $this->actingAs($this->adminUser())->get("/animals/{$animal->id}/tag");

        $response->assertOk();
        $response->assertSee('TAG-0042');
    }

    public function test_the_scan_page_is_reachable(): void
    {
        $response = $this->actingAs($this->adminUser())->get('/scan');

        $response->assertOk();
    }
}
