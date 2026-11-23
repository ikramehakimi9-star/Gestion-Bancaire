<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Compte;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelationTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_client_peut_avoir_plusieurs_comptes(): void
    {
        $client = Client::factory()->create();
        Compte::factory(3)->create(['client_id' => $client->id]);

        $this->assertCount(3, $client->comptes);
    }

    public function test_un_compte_appartient_a_un_seul_client(): void
    {
        $compte = Compte::factory()->create();

        $this->assertInstanceOf(Client::class, $compte->client);
    }

    public function test_supprimer_un_client_supprime_ses_comptes(): void
    {
        $client = Client::factory()->create();
        Compte::factory(2)->create(['client_id' => $client->id]);

        $client->delete();

        $this->assertDatabaseCount('comptes', 0);
    }
}
