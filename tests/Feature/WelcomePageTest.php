<?php

namespace Tests\Feature;

use Tests\TestCase;

class WelcomePageTest extends TestCase
{
    public function test_welcome_page_does_not_show_register_link(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Crear cuenta');
    }
}
