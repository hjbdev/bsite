<?php

namespace App\Console\Commands;

use App\Models\Team;
use Illuminate\Console\Command;

class SyncEseaMatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-esea-matches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $teamsWithFaceitId = Team::whereNotNull('faceit_id')->get();

        foreach ($teamsWithFaceitId as $team) {
            $this->call('app:sync-esea-matches-for-team', [
                'teamId' => $team->id,
            ]);
        }
    }
}
