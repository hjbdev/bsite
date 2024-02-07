<?php

namespace App\Jobs\Players;

use App\Actions\SeriesMap\GetCachedSeriesMap;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class ResetPlayerSeriesMapHealth implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $seriesMapId, public Carbon $logReceivedAt)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $seriesMap = app(GetCachedSeriesMap::class)->execute($this->seriesMapId);

        if (! $seriesMap->start_date) {
            // If the series map doesn't have a start date, we don't want to update the stats
            return;
        }

        if ($seriesMap->start_date->gte($this->logReceivedAt)) {
            // If the log was before the series map started, we don't want to update the stats
            return;
        }

        $seriesMap->players()->newPivotQuery()->update([
            'health' => 100,
        ]);
    }
}
