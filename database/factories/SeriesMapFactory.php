<?php

namespace Database\Factories;

use App\Models\Map;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SeriesMap>
 */
class SeriesMapFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isFinished = fake()->boolean();

        $winner = $isFinished ? fake()->randomElement(['a', 'b']) : null;

        $teamAScore = null;
        $teamBScore = null;

        if ($winner) {
            if ($winner === 'a') {
                $teamAScore = 16;
                $teamBScore = $this->faker->numberBetween(0, 14);
            } else {
                $teamAScore = $this->faker->numberBetween(0, 14);
                $teamBScore = 16;
            }
        }

        return [
            'status' => $isFinished ? 'finished' : 'upcoming',
            'start_date' => $this->faker->dateTimeBetween('-1 year', '+1 month'),
            'map_id' => Map::inRandomOrder()->first(['id'])->id,
            'team_a_score' => $isFinished ? $teamAScore : 0,
            'team_b_score' => $isFinished ? $teamBScore : 0,
        ];
    }
}
