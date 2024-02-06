<?php

namespace App\Console\Commands;

use App\Enums\SeriesMapStatus;
use App\Enums\SeriesStatus;
use App\Models\Event;
use App\Models\Map;
use App\Models\Series;
use App\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

class SyncFaceitMatchesForEvent extends Command
{
    protected $signature = 'app:sync-faceit-matches-for-event {eventId}';
    protected $description = 'Synchronise with faceit for the given event.';

    public function handle()
    {
        $event = Event::findOrFail($this->argument('eventId'));

        $limit = 20;
        $offset = 0;
        $lastCount = 1;

        while ($offset % $limit === 0 && $lastCount !== 0) {
            $response = $this->performFaceitCall($event->faceit_championship_id, $offset, $limit);

            if (!$response->ok()) {
                error('Failed');
                dd($response->body());
            }

            foreach ($response->json('items') as $match) {
                info("Importing match {$match['match_id']} for event {$event->name}");

                $series = Series::firstOrNew([
                    'event_id' => $event->id,
                    'source_id' => $match['match_id'],
                ]);

                foreach (['A', 'B'] as $uppercase) {
                    $number = $uppercase === 'A' ? 1 : 2;
                    $team = Team::where('faceit_id', $match['teams']['faction' . $number]['faction_id'])
                        ->orWhere('secondary_faceit_id', $match['teams']['faction' . $number]['faction_id'])
                        ->first();

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

                if (Arr::has($match, 'results')) {
                    $series->team_a_score = Arr::get($match, 'results.score.faction1');
                    $series->team_b_score = Arr::get($match, 'results.score.faction2');
                }

                try {
                    $series->save();
                } catch (\Exception $e) {
                    error('Failed to save series');
                    error($e->getMessage());
                    continue;
                }

                if (count($maps = Arr::get($match, 'voting.map.pick', [])) > 0) {
                    foreach ($maps as $map) {
                        $series->seriesMaps()->updateOrCreate([
                            'map_id' => Map::firstWhere('name', $map)->id,
                        ], [
                            'status' => SeriesMapStatus::UPCOMING,
                        ]);
                    }
                }
            }

            $lastCount = count($response->json('items'));
            $offset = $response->json('end');

            sleep(0.1);

            info('Fetched page ' . (($offset / $limit)) . ' for ' . $event->name);
        }

        // reset offset
        $offset = 0;
        $lastCount = 1;
    }

    protected function performFaceitCall(string $championshipId, int $offset, int $limit): Response
    {
        return Http::withHeader('Authorization', 'Bearer ' . config('services.faceit.token'))
            ->get('https://open.faceit.com/data/v4/championships/' . $championshipId . '/matches?offset=' . $offset . '&limit=' . $limit);
    }
}
