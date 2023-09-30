<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $organisers = ['BLAST', 'ESL', 'EPICLAN', 'Insomnia'];
        $events = ['Pro League', 'Premier', 'Major'];
        $distinguishers = ['Qualifier', 'Finals'];

        $name = '';

        $name .= $this->faker->randomElement($organisers) . ' ';
        $name .= $this->faker->randomElement($events) . ' ';
        $name .= $this->faker->randomElement([...$distinguishers, $this->faker->randomNumber(2)]);

        $startDate = $this->faker->dateTimeBetween('-1 year', '+1 year');

        return [
            'name' => $name,
            'description' => $this->faker->paragraph(),
            'start_date' => $startDate,
            'end_date' => Carbon::instance($startDate)->addDays($this->faker->numberBetween(3, 7)),
        ];
    }
}
