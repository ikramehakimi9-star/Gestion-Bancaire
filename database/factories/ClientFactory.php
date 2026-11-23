<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nom'       => fake()->lastName(),
            'prenom'    => fake()->firstName(),
            'cin'       => strtoupper(fake()->unique()->bothify('??######')),
            'telephone' => '06' . fake()->numerify('########'),
            'email'     => fake()->unique()->safeEmail(),
            'adresse'   => fake()->address(),
        ];
    }
}
