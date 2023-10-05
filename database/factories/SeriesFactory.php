<?php

namespace Database\Factories;

use App\Enums\SeriesStatus;
use App\Models\Event;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Series>
 */
class SeriesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $teamA = Team::inRandomOrder()->first();
        $teamB = Team::inRandomOrder()->where('id', '!=', $teamA->id)->first();

        return [
            'event_id' => Event::inRandomOrder()->first(['id'])->id,
            'team_a_id' => $teamA->id,
            'team_b_id' => $teamB->id,
            'team_a_score' => $this->faker->numberBetween(0, 2),
            'team_b_score' => $this->faker->numberBetween(0, 2),
            'type' => $this->faker->randomElement(['bo1', 'bo3', 'bo5']),
            'status' => $this->faker->randomElement([SeriesStatus::UPCOMING, SeriesStatus::ONGOING, SeriesStatus::FINISHED]),
            'start_date' => $this->faker->dateTimeBetween('-1 year', '+1 month'),
        ];
    }
}
