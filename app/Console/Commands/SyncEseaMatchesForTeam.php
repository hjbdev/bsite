<?php

namespace App\Console\Commands;

use App\Enums\SeriesStatus;
use App\Models\Event;
use App\Models\Series;
use App\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class SyncEseaMatchesForTeam extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-esea-matches-for-team {teamId}';

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
        $team = Team::findOrFail($this->argument('teamId'));

        $this->info('Syncing ESEA matches for ' . $team->name);

        if (!$team->faceit_id) {
            $this->error('Team does not have a Faceit ID');

            return;
        }

        $response = Http::withHeader('Authorization', config('services.stratbox_esea.token'))
            ->get('https://esea.stratbox.app/api/teams/' . $team->faceit_id . '/fixtures');

        if ($response->failed()) {
            $this->error('Failed to fetch ESEA matches');

            return;
        }

        $matches = $response->json();

        foreach ($matches as $match) {
            // First, figure out if our team is team_a or team_b
            $isTeamA = $match['team_a']['faceit_id'] === $team->faceit_id;
            
            // Next, figure out if the _other team_ is part of the database
            $otherTeam = Team::where('faceit_id', $isTeamA ? $match['team_b']['faceit_id'] : $match['team_a']['faceit_id'])->first();

            // Now, we check if the series already exists
            $series = Series::where('source', 'esea')
                ->where('source_id', $match['faceit_id'])
                ->first();

            $shouldBeTeamA = $otherTeam ? $isTeamA : true;

            if (!$series) {
                $bestOf = Arr::get($match, 'best_of', 1);
                $type = $bestOf === 1 ? 'bo1' : ($bestOf === 3 ? 'bo3' : 'bo5');

                $series = new Series([
                    'source' => 'esea',
                    'source_id' => $match['faceit_id'],
                    'team_a_id' => $shouldBeTeamA ? $team->id : $otherTeam?->id,
                    'team_b_id' => $shouldBeTeamA ? $otherTeam?->id : $team->id,
                    'type' => $type,
                    'event_id' => Event::where('faceit_division_id', $match['division_id'])->firstOrFail()->id
                ]);
            }

            // if ($matchMaps = Arr::get($match, 'maps')) {
            //     foreach ($matchMaps as $matchMap) {
            //         $series->seriesMaps()->updateOrCreate([
            //             'map' => $matchMap['map']
            //         ], [
            //             // 'team_a_score' => $matchMap['score']['faction1'],
            //             // 'team_b_score' => $matchMap['score']['faction2']
            //         ]);
            //     }
            // }

            if (! $otherTeam) {
                $series->team_b_data = $isTeamA ? $match['team_b'] : $match['team_a'];
                $series->team_b_name = $isTeamA ? $match['team_b']['name'] : $match['team_a']['name'];
            }

            if (isset($match['results']['score']['faction1']) && isset($match['results']['score']['faction2'])) {
                $series->team_a_score = ($shouldBeTeamA ? $match['results']['score']['faction1'] : $match['results']['score']['faction2']) ?? 0;
                $series->team_b_score = ($shouldBeTeamA ? $match['results']['score']['faction2'] : $match['results']['score']['faction1']) ?? 0;
            }

            $series->start_date = now()->parse($match['scheduled_at']);
            $series->status = $match['status'] === 'FINISHED' ? SeriesStatus::FINISHED : SeriesStatus::UPCOMING;

            $series->save();

            $this->info('Saved series ' . $series->teamA->name . ' vs ' . ($series->teamB?->name ?? $series->team_b_name));
        }


        $this->info('Found ' . count($matches) . ' matches');
    }
}
