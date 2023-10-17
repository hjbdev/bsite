<?php

namespace App\Jobs\SeriesMaps;

use App\Actions\Players\GetCachedPlayerWithSteamId3;
use App\Actions\Series\GetCachedSeries;
use App\Actions\SeriesMap\GetCachedSeriesMap;
use App\Jobs\Players\ModifyPlayerSeriesMapStatistic;
use App\Models\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecordOpenings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $seriesMapId,
        public int $logId,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $seriesMap = app(GetCachedSeriesMap::class)->execute($this->seriesMapId);
        $series = app(GetCachedSeries::class)->execute($seriesMap->series_id);
        $log = Log::findOrFail($this->logId);
        $killer = app(GetCachedPlayerWithSteamId3::class)->execute($log->data['killerSteamId']);
        $killed = app(GetCachedPlayerWithSteamId3::class)->execute($log->data['killedSteamId']);

        // Need to find the log at the start of the round.
        $roundStartLog = Log::where('series_id', $seriesMap->series_id)
            ->where('id', '<', $log->id)
            ->where('type', 'MatchStatus')
            ->orderBy('id', 'desc')
            ->first();

        if (! $roundStartLog) {
            return;
        }

        // Now we know the id range from the start of the round to now, lets figure out
        // if this is an opening kill
        $previousKillInRound = Log::where('series_id', $seriesMap->series_id)
            ->where('id', '>', $roundStartLog->id)
            ->where('id', '<', $log->id)
            ->where('type', 'Kill')
            ->exists();

        if (! $previousKillInRound) {
            // This is an opening kill
            if ($killer) {
                dispatch(new ModifyPlayerSeriesMapStatistic(
                    playerId: $killer->id,
                    seriesMapId: $seriesMap->id,
                    statistic: 'opening_kills',
                    value: 1,
                    logReceivedAt: $log->created_at
                ))->delay($series->event->delay);
            }
            if ($killed) {
                dispatch(new ModifyPlayerSeriesMapStatistic(
                    playerId: $killed->id,
                    seriesMapId: $seriesMap->id,
                    statistic: 'opening_deaths',
                    value: 1,
                    logReceivedAt: $log->created_at
                ))->delay($series->event->delay);
            }
        }
    }
}
