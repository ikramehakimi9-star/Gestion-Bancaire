<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_liste_des_clients_est_triee_par_nom(): void
    {
        Client::factory()->create(['nom' => 'Zahiri']);
        Client::factory()->create(['nom' => 'Alami']);

        $this->get(route('clients.index'))
            ->assertOk()
            ->assertSeeInOrder(['Alami', 'Zahiri']);
    }

    public function test_on_peut_creer_un_client(): void
    {
        $data = Client::factory()->make()->toArray();

        $this->post(route('clients.store'), $data)
            ->assertRedirect(route('clients.index'));

        $this->assertDatabaseHas('clients', ['cin' => $data['cin']]);
    }

    public function test_les_champs_obligatoires_sont_valides(): void
    {
        $this->post(route('clients.store'), [])
            ->assertSessionHasErrors(['nom', 'prenom', 'cin', 'telephone', 'email']);
    }

    public function test_le_cin_et_l_email_doivent_etre_uniques(): void
    {
        $existant = Client::factory()->create();
        $data = Client::factory()->make([
            'cin'   => $existant->cin,
            'email' => $existant->email,
        ])->toArray();

        $this->post(route('clients.store'), $data)
            ->assertSessionHasErrors(['cin', 'email']);
    }

    public function test_on_peut_modifier_et_supprimer_un_client(): void
    {
        $client = Client::factory()->create();

        // Modification sans changer le CIN ni l'email : doit passer
        $data = $client->toArray();
        $data['nom'] = 'Nouveau';

        $this->put(route('clients.update', $client), $data)
            ->assertRedirect(route('clients.index'));
        $this->assertDatabaseHas('clients', ['id' => $client->id, 'nom' => 'Nouveau']);

        $this->delete(route('clients.destroy', $client))
            ->assertRedirect(route('clients.index'));
        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }
}
