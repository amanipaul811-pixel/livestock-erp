<?php

namespace Tests\Feature\Web;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrustedProxyTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_login_form_posts_to_https_when_the_proxy_forwards_https(): void
    {
        $response = $this->withHeaders(['X-Forwarded-Proto' => 'https'])->get('/login');

        $response->assertOk();
        $response->assertSee('action="https://', false);
    }
}
