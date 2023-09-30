<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Player>
 */
class PlayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->userName(),
            'full_name' => $this->faker->name(),
            'nationality' => $this->faker->countryCode(),
            'birthday' => $this->faker->dateTimeBetween('-30 years', '-15 years'),
        ];
    }
}
