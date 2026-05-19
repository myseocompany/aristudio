<?php

namespace Tests\Feature;

use Tests\TestCase;

class LuganoLandingTest extends TestCase
{
    public function test_lugano_landing_has_whatsapp_bubble(): void
    {
        $html = (string) file_get_contents(public_path('lugano/index.html'));

        $this->assertStringContainsString('class="whatsapp-bubble"', $html);
        $this->assertStringContainsString('href="https://wa.me/573126050467?text=Hola%2C%20quiero%20hacer%20una%20reserva%20en%20unlugar."', $html);
        $this->assertStringContainsString('aria-label="Escribir por WhatsApp"', $html);
    }
}
