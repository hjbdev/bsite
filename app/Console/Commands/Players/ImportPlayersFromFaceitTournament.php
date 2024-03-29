<?php

namespace App\Console\Commands\Players;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportPlayersFromFaceitTournament extends Command
{
    protected $signature = 'app:import-players-from-faceit-tournament {tournamentId} {--offset=0}';

    protected $description = 'Syncs players from faceit tournament';

    public function handle()
    {
        $response = Http::withHeader('Authorization', 'Bearer '.env('FACEIT_API_TOKEN'))->get('https://open.faceit.com/data/v4/championships/'.$this->argument('tournamentId').'/subscriptions?offset='.$this->option('offset').'&limit=10');

        if (! $response->ok()) {
            $this->error('Error while fetching players from faceit');
            dd($response->body());
        }

        $teams = $response->json('items');

        foreach ($teams as $team) {
            $this->info('Importing team '.$team['team']['name']);

            if (! $teamModel = Team::where('faceit_id', $team['team']['team_id'])->exists()) {
                $teamModel = new Team;
                $teamModel->faceit_id = $team['team']['team_id'];
                $teamModel->name = $team['team']['name'];
                $teamModel->save();
            } else {
                $teamModel = Team::where('faceit_id', $team['team']['team_id'])->first();
            }

            foreach ($team['roster'] as $rosterId) {
                $response = Http::withHeader('Authorization', 'Bearer '.env('FACEIT_API_TOKEN'))->get('https://open.faceit.com/data/v4/players/'.$rosterId);

                if (! $response->ok()) {
                    $this->error('Error while fetching player '.$rosterId.'  from faceit');

                    continue;
                }

                $player = $response->json();

                $this->info('Importing player '.$player['nickname']);

                $playerModel = new Player;

                if (! $playerModel->where('faceit_id', $player['player_id'])->exists()) {
                    $playerModel->faceit_id = $player['player_id'];
                    $playerModel->name = $player['nickname'];
                    $playerModel->nationality = str($player['country'])->upper();
                    $playerModel->save();
                } else {
                    $playerModel = $playerModel->where('faceit_id', $player['player_id'])->first();
                }

                $playerModel->steam_id64 = $player['games']['cs2']['game_player_id'];
                $playerModel->save();

                $teamModel->players()->syncWithoutDetaching([$playerModel->id => ['start_date' => now()]]);
            }

            // Substitutes
            foreach ($team['substitutes'] as $rosterId) {
                $response = Http::withHeader('Authorization', 'Bearer '.env('FACEIT_API_TOKEN'))->get('https://open.faceit.com/data/v4/players/'.$rosterId);

                if (! $response->ok()) {
                    $this->error('Error while fetching player '.$rosterId.'  from faceit');

                    continue;
                }

                $player = $response->json();

                $this->info('Importing player '.$player['nickname']);

                $playerModel = new Player;

                if (! $playerModel->where('faceit_id', $player['player_id'])->exists()) {
                    $playerModel->faceit_id = $player['player_id'];
                    $playerModel->name = $player['nickname'];
                    $playerModel->nationality = str($player['country'])->upper();
                    $playerModel->save();
                } else {
                    $playerModel = $playerModel->where('faceit_id', $player['player_id'])->first();
                }

                $playerModel->steam_id64 = $player['games']['cs2']['game_player_id'];
                $playerModel->save();

                $teamModel->players()->syncWithoutDetaching([$playerModel->id => ['start_date' => now(), 'substitute' => true]]);
            }
        }

        if (count($teams) === 10) {
            $this->call('app:import-players-from-faceit-tournament', [
                'tournamentId' => $this->argument('tournamentId'),
                '--offset' => intval($this->option('offset')) + 10,
            ]);
        }
    }
}
