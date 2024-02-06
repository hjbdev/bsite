<?php

namespace App\Jobs\Series;

use App\Enums\SeriesMapStatus;
use App\Enums\SeriesStatus;
use App\Models\Event;
use App\Models\Map;
use App\Models\Player;
use App\Models\Series;
use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class UpdateFromFaceit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $faceitId,
        public string $hookEvent
    ) {
        //
    }

    public function handle(): void
    {
        $response = Http::withHeader('Authorization', 'Bearer ' . config('services.faceit.token'))
            ->get('https://open.faceit.com/data/v4/matches/' . $this->faceitId);

        if (!$response->ok()) {
            logger('Failed to fetch match from faceit');
            dd($response->body());
        }

        $match = $response->json();
        $event = Event::firstWhere('faceit_championship_id', $match['competition_id']);

        if (!$event) {
            logger('Event not found');
            return;
        }

        $series = Series::firstOrNew([
            'event_id' => $event->id,
            'source_id' => $match['match_id'],
        ]);

        foreach (['A', 'B'] as $uppercase) {
            $number = $uppercase === 'A' ? 1 : 2;
            $team = Team::firstWhere('faceit_id', $match['teams']['faction' . $number]['faction_id']);

            if ($team) {
                $series->{'team' . $uppercase}()->associate($team);
            }

            info("Team {$uppercase} is " . $team?->name ?? 'Unknown');
        }

        $series->type = 'bo' . Arr::get($match, 'best_of', 1);
        $series->start_date = now()->parse(Arr::get($match, 'scheduled_at'));
        $series->source = 'faceit';

        if (Arr::get($match, 'status') === 'FINISHED') {
            $series->status = SeriesStatus::FINISHED;
        } else if (Arr::get($match, 'status') === 'CANCELLED') {
            $series->status = SeriesStatus::CANCELLED;
        } else if (in_array(Arr::get($match, 'status'), ['ONGOING', 'READY', 'CONFIGURING', 'VOTING', 'SUBSTITUTION', 'CAPTAIN_PICK'])) {
            $series->status = SeriesStatus::ONGOING;
        } else {
            $series->status = SeriesStatus::UPCOMING;
        }

        $series->saveQuietly();

        $statsResponse = null;

        if (count($maps = Arr::get($match, 'voting.map.pick', [])) > 0) {
            foreach ($maps as $map) {
                $series->seriesMaps()->updateOrCreate([
                    'map_id' => Map::firstWhere('name', $map)->id,
                ], [
                    'status' => SeriesMapStatus::UPCOMING,
                ]);
            }
        }

        if (true || $series->isDirty('status') && $series->status === SeriesStatus::FINISHED) {
            $statsResponse = Http::withHeader('Authorization', 'Bearer ' . config('services.faceit.token'))
                ->get('https://open.faceit.com/data/v4/matches/' . $this->faceitId . '/stats');

            if (!$statsResponse->ok()) {
                logger('Failed to fetch match stats from faceit');
            } else {
                foreach (Arr::get($statsResponse->json(), 'rounds', []) as $round) {
                    $seriesMap = $series->seriesMaps()->firstOrNew([
                        'map_id' => Map::firstWhere('name', $round['round_stats']['Map'])->id,
                    ]);

                    list($teamAScore, $teamBScore) = explode(' / ', $round['round_stats']['Score']);

                    $seriesMap->team_a_score = intval($teamAScore);
                    $seriesMap->team_b_score = intval($teamBScore);
                    $seriesMap->status = SeriesMapStatus::FINISHED;

                    $seriesMap->save();

                    foreach (Arr::get($round, 'teams', []) as $team) {
                        foreach (Arr::get($team, 'players', []) as $player) {
                            // First try to find the player by faceit ID, if not, look up their steam id and try that
                            $playerModel = Player::firstWhere('faceit_id', $player['player_id']);

                            if (!$playerModel) {
                                $playerResponse = Http::withHeader('Authorization', 'Bearer ' . config('services.faceit.token'))
                                    ->get('https://open.faceit.com/data/v4/players/' . $player['player_id']);
                                if (!$playerResponse->ok()) {
                                    continue;
                                }

                                $playerData = $playerResponse->json();
                                $playerModel = Player::firstWhere('steam_id64', Arr::get($playerData, 'games.cs2.game_player_id'));

                                if (! $playerModel) {
                                    logger('Could not find profile for player ' . Arr::get($playerData, 'nickname'));
                                    continue;
                                }

                                $pivotData = [
                                    'kills' => Arr::get($player, 'player_stats.Kills'),
                                    'assists' => Arr::get($player, 'player_stats.Assists'),
                                    'deaths' => Arr::get($player, 'player_stats.Deaths'),
                                ];

                                if (! $seriesMap->players()->where('id', $playerModel->id)->exists()) {
                                    $seriesMap->players()->attach($playerModel->id, $pivotData);
                                } else {
                                    $seriesMap->players()->updateExistingPivot($playerModel->id, $pivotData);
                                }
                            }
                        }
                    }
                }
            }
        }

        if (Arr::has($match, 'results')) {
            $series->team_a_score = Arr::get($match, 'results.score.faction1');
            $series->team_b_score = Arr::get($match, 'results.score.faction2');
        }

        try {
            $series->save();
        } catch (\Exception $e) {
            logger('Failed to save series');
            logger($e->getMessage());
            return;
        }
    }
}
