<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hero>
 */
class HeroFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $names = [
            'Mixart', 'Krisda', 'Croix7','Ernie', 'Rediqui', 'Krisda2', 'JerryCost'
        ];

        $races = [
            'Humano', 'Elfo', 'Elfo Oscuro', 'Gigante', 'Enano'
        ];

        $roles = [
            'Espadachín', 'Arquero', 'Mago', 'Asesino', 'Valkiria',
            'Caballero', 'Explorador', 'Sanador'
        ];

        return [
            'name_hero' => fake()->unique()->randomElement($names),
            'race_hero' => fake()->randomElement($races),
            'role_hero' => fake()->randomElement($roles),
        ];
    }
}