<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BaseSeeder extends Seeder
{
    public function run(): void
    {
        app(MapSeeder::class)->run();
    }
}
