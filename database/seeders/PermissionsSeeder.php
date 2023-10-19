<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Silber\Bouncer\BouncerFacade as Bouncer;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bouncer::allow('super-admin')->everything();

        Bouncer::allow('admin')->to([
            'store:(own)series',
            'update:(own)series',
            'destroy:(own)series',

            'store:stream',
            'destroy:stream',

            'update:(own)organiser',

            'store:(own)event',
            'update:(own)event',
            'destroy:(own)event',

            'store:(own)series-map',
            'update:(own)series-map',
            'destroy:(own)series-map',

            'store:(own)veto',
            'update:(own)veto',
            'destroy:(own)veto',

            'store:player',
            'update:player',
            'destroy:player',

            'store:team',
            'update:team',
            'destroy:team',
        ]);
    }
}
