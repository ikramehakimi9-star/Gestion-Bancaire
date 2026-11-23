<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Compte;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // 10 clients, chacun avec 1 à 3 comptes
        Client::factory(10)->create()->each(function (Client $client) {
            Compte::factory(rand(1, 3))->create(['client_id' => $client->id]);
        });
    }
}
