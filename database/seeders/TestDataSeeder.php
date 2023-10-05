<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Player;
use App\Models\Series;
use App\Models\SeriesMap;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'email' => 'harry@hjb.dev',
            'name' => 'index'
        ]);

        Team::factory(200)
            ->has(Player::factory()->count(5), 'players')
            ->create();

        Event::factory(20)
            ->has(
                Series::factory()
                    ->has(SeriesMap::factory()->count(fake()->randomElement([1, 3])))
                    ->count(fake()->numberBetween(5, 20))
            )
            ->create();

        $p = Player::factory()->create([
            'name' => 'index',
            'steam_id3' => '[U:1:98202654]',
            'steam_id64' => '76561198058468382',
        ]);

        $t = Team::factory()->has(Player::factory()->count(4))->create([
            'name' => 'undefined'
        ]);

        $t->players()->attach($p);

        $e = Event::first();
        $e->series()
            ->save(
                Series::factory()/*->has(
                    SeriesMap::factory()->count(fake()->randomElement([1, 3]))
                )*/->create([
                    'team_a_id' => $t->id
                ])
            );
    }
}
