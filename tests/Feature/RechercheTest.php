<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Compte;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RechercheTest extends TestCase
{
    use RefreshDatabase;

    public function test_on_peut_chercher_un_client_par_nom(): void
    {
        Client::factory()->create(['nom' => 'Alaoui']);
        Client::factory()->create(['nom' => 'Benani']);

        $this->get(route('clients.index', ['q' => 'Alao']))
            ->assertOk()
            ->assertSee('Alaoui')
            ->assertDontSee('Benani');
    }

    public function test_une_recherche_client_sans_resultat_affiche_un_message(): void
    {
        Client::factory()->create(['nom' => 'Alaoui']);

        $this->get(route('clients.index', ['q' => 'zzzz']))
            ->assertOk()
            ->assertSee('Aucun client ne correspond');
    }

    public function test_une_liste_de_clients_vide_propose_d_en_ajouter(): void
    {
        $this->get(route('clients.index'))
            ->assertOk()
            ->assertSee('Aucun client enregistré');
    }

    public function test_on_peut_chercher_un_compte_par_rib_avec_des_espaces(): void
    {
        $trouve = Compte::factory()->create(['rib' => '123456789012345678901234']);
        $autre  = Compte::factory()->create(['rib' => '999999999999999999999999']);

        $this->get(route('comptes.index', ['q' => '1234 5678 9012']))
            ->assertOk()
            ->assertSee($trouve->rib_formate)
            ->assertDontSee($autre->rib_formate);
    }

    public function test_on_peut_chercher_un_compte_par_nom_du_titulaire(): void
    {
        $client = Client::factory()->create(['nom' => 'Zahiri']);
        $trouve = Compte::factory()->create(['client_id' => $client->id]);
        $autre  = Compte::factory()->create();

        $this->get(route('comptes.index', ['q' => 'Zahiri']))
            ->assertOk()
            ->assertSee($trouve->rib_formate)
            ->assertDontSee($autre->rib_formate);
    }
}
