<?php

namespace App\Jobs\SeriesMaps;

use App\Actions\SeriesMap\GetCachedSeriesMap;
use App\Models\Series;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class SetSeriesMapRoundsPlayed implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $seriesMapId, public int $roundsPlayed, public Carbon $logReceivedAt)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $seriesMap = app(GetCachedSeriesMap::class)->execute($this->seriesMapId);

        if (!$seriesMap) {
            return;
        }

        if ($seriesMap->start_date->gte($this->logReceivedAt)) {
            // If the log was before the series map started, we don't want to update the stats
            return;
        }

        $seriesMap->rounds_played = $this->roundsPlayed;
        $seriesMap->save();

        Cache::put('series-map-' . $seriesMap->id, $seriesMap, Series::CACHE_TTL);
    }
}
