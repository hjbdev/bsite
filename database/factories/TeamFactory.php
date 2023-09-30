<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $organisations = [
            'Team Liquid',
            'Team Secret',
            'OG',
            'Evil Geniuses',
            'Virtus.pro',
            'fnatic',
            'Natus Vincere',
            'GamerLegion',
            'AimerLegion',
            'Team Spirit',
            'FaZe',
            'G2',
            'Astralis',
            'Ninjas in Pyjamas',
            'Cloud9',
            'MIBR',
            'FURIA',
            'Gambit',
            'Vitality',
            'ENCE',
            'mousesports',
            'BIG',
            'Complexity',
            'Heroic',
            'North',
            'Renegades',
            '100 Thieves',
            'TYLOO',
            'ViCi',
            'Grayhound',
            'ORDER',
            'AVANT',
            'Ground Zero',
            'Paradox',
            'Rooster',
            'Chiefs',
            'Tainted Minds',
            'BTRG',
            'Lucid Dream',
        ];

        $name = "";

        $disambiguation = $this->faker->numberBetween(1, 9);

        $name .= $this->faker->randomElement($organisations);
        $name .= " " . $disambiguation;

        return [
            'name' => $name,
        ];
    }
}
