<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Compte;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_dashboard_affiche_les_statistiques(): void
    {
        Client::factory(2)->create();
        Compte::factory()->create(['solde' => 1000]);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Tableau de bord')
            ->assertViewHas('stats');
    }
}
