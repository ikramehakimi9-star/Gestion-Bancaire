<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Compte;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompteTest extends TestCase
{
    use RefreshDatabase;

    public function test_on_peut_creer_un_compte(): void
    {
        $client = Client::factory()->create();

        $this->post(route('comptes.store'), [
            'rib'       => '123456789012345678901234',
            'solde'     => 1500,
            'client_id' => $client->id,
        ])->assertRedirect(route('comptes.index'));

        $this->assertDatabaseHas('comptes', [
            'rib'       => '123456789012345678901234',
            'client_id' => $client->id,
        ]);
    }

    public function test_le_solde_vaut_zero_par_defaut(): void
    {
        $client = Client::factory()->create();

        $this->post(route('comptes.store'), [
            'rib'       => '111111111111111111111111',
            'client_id' => $client->id,
        ]);

        $this->assertEquals(0, Compte::first()->solde);
    }

    public function test_le_rib_doit_contenir_24_chiffres(): void
    {
        $client = Client::factory()->create();

        $this->post(route('comptes.store'), [
            'rib'       => '12345',
            'solde'     => 100,
            'client_id' => $client->id,
        ])->assertSessionHasErrors('rib');
    }

    public function test_le_rib_doit_etre_unique(): void
    {
        $compte = Compte::factory()->create();

        $this->post(route('comptes.store'), [
            'rib'       => $compte->rib,
            'solde'     => 100,
            'client_id' => $compte->client_id,
        ])->assertSessionHasErrors('rib');
    }

    public function test_solde_negatif_et_client_inexistant_sont_refuses(): void
    {
        $this->post(route('comptes.store'), [
            'rib'       => '222222222222222222222222',
            'solde'     => -50,
            'client_id' => 9999,
        ])->assertSessionHasErrors(['solde', 'client_id']);
    }
}
