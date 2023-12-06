<?php

namespace App\Console\Commands\Players;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportPlayersFromEpiclanMasterSheet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-players-from-epiclan-master-sheet {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports the players from the EPICLAN master sheet as csv.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! file_exists(storage_path($this->argument('filename').'.csv'))) {
            $this->error('File does not exist');

            return;
        }

        $file = fopen(storage_path($this->argument('filename').'.csv'), 'r');

        // $csv = fgetcsv($file, 500, ',');

        $contents = collect();

        while (($data = fgetcsv($file, 1000, ',')) !== false) {
            $contents->push($data);
        }

        $lastTeam = null;

        $createdTeams = 0;
        $createdPlayers = 0;

        foreach ($contents as $row) {
            [$teamNumber, $name, $steamId64] = $row;

            if ($teamNumber && $name) {
                // this is a team
                $lastTeam = Team::updateOrCreate([
                    'name' => $name,
                ]);
                $createdTeams++;
            }

            if (! $teamNumber && $name && $steamId64) {
                // this could be a player
                if (! $lastTeam) {
                    continue;
                }

                $player = Player::updateOrCreate([
                    'steam_id64' => $steamId64,
                ], [
                    'name' => $name,
                ]);
                $createdPlayers++;

                if (! $lastTeam->players()->where('id', $player->id)->exists()) {
                    $lastTeam->players()->syncWithoutDetaching([$player->id => [
                        'start_date' => Carbon::createFromFormat('Y-m-d', '2023-10-26'),
                        'end_date' => Carbon::createFromFormat('Y-m-d', '2023-10-29'),
                        'substitute' => false,
                    ]]);
                    // $teamModel->players()->syncWithoutDetaching([$playerModel->id => ['start_date' => now(), 'substitute' => true]]);
                }

                // $lastTeam->players()->updateOrCreate([
                //     'steam_id64' => $steamId64,
                // ], [

                // ]);
            }

        }

        $this->info('Created '.$createdTeams.' teams');
        $this->info('Created '.$createdPlayers.' players');
    }
}
