<?php

namespace Database\Seeders;

use App\Models\Map;
use Illuminate\Database\Seeder;

class MapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maps = [
            [
                'title' => 'Mirage',
                'name' => 'de_mirage',
            ],
            [
                'title' => 'Inferno',
                'name' => 'de_inferno',
            ],
            [
                'title' => 'Nuke',
                'name' => 'de_nuke',
            ],
            [
                'title' => 'Overpass',
                'name' => 'de_overpass',
            ],
            [
                'title' => 'Vertigo',
                'name' => 'de_vertigo',
            ],
            [
                'title' => 'Ancient',
                'name' => 'de_ancient',
            ],
            [
                'title' => 'Anubis',
                'name' => 'de_anubis',
            ],
        ];

        foreach ($maps as $map) {
            Map::create($map);
        }
    }
}
