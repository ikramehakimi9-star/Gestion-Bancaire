<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'rib'       => fake()->unique()->numerify(str_repeat('#', 24)),
            'solde'     => fake()->randomFloat(2, 0, 50000),
            'client_id' => Client::factory(),
        ];
    }
}
