<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mission>
 */
class MissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titles = [
            'Rescatar el gato de la abuelita',
            'Compra Ropa',
            'Matar 20 cerdos salvajes llamados Ernie',
            'Recuperar el territorio que estan invadido por los Balas',
            'Matar a Dios'
        ];

        $descriptions = [
            'Aqui deberia de haber una descripcion',
            'Parece que aqui debio estar una descripcion',
            'Descripcion no disponible',
            'Sin descripcion',
            'No quise poner descripcion'
        ];

        $difficulties = ['easy', 'medium', 'hard', 'extreme', 'yes'];
        $statuses = ['starting','pending', 'in_progress', 'completed', 'cancelled', 'failed'];

        $titleIndex = fake()->unique()->numberBetween(0, count($titles) - 1);

        return [
            'title_mission' => $titles[$titleIndex],
            'description_mission' => $descriptions[$titleIndex],
            'difficulty_mission' => fake()->randomElement($difficulties),
            'status_mission' => fake()->randomElement($statuses),
        ];
    }
}