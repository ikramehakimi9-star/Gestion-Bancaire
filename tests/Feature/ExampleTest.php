<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_la_racine_redirige_vers_les_clients(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('dashboard'));
    }
}
