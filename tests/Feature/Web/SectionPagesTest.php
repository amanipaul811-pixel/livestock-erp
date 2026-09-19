<?php

namespace Tests\Feature\Web;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class SectionPagesTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_guests_are_sent_to_login(): void
    {
        $this->get('/inventory')->assertRedirect('/login');
    }

    public function test_each_section_page_lists_its_links(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)->get('/inventory')->assertOk()
            ->assertSee('Feed Items')->assertSee('Warehouses')->assertSee('Ration Formulas')->assertSee('Stock Report');

        $this->actingAs($admin)->get('/purchase')->assertOk()
            ->assertSee('Purchase Orders')->assertSee('Suppliers')->assertSee('Purchases Report');

        $this->actingAs($admin)->get('/sales')->assertOk()
            ->assertSee('New Sale')->assertSee('Customers')->assertSee('Species Pricing')->assertSee('Sales Report');
    }

    public function test_links_a_user_lacks_permission_for_are_hidden(): void
    {
        $response = $this->actingAs($this->userWithRole('Feeder'))->get('/sales');

        $response->assertOk();
        $response->assertDontSee('New Sale');
        $response->assertSee('Customers');
    }

    public function test_a_list_page_goes_back_to_its_section_and_the_section_goes_back_to_the_dashboard(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)->get('/feed-items')
            ->assertSee('href="'.route('sections.inventory').'"', false);

        $this->actingAs($admin)->get('/inventory')
            ->assertSee('title="Back"', false)
            ->assertSee('href="'.route('dashboard').'"', false);
    }
}
