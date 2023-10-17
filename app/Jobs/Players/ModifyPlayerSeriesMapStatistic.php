<?php

namespace App\Jobs\Players;

use App\Actions\SeriesMap\GetCachedSeriesMap;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ModifyPlayerSeriesMapStatistic implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $playerId, public int $seriesMapId, public string $statistic, public int $value, public Carbon $logReceivedAt, public $operator = '+', public $victimId = null)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $seriesMap = app(GetCachedSeriesMap::class)->execute($this->seriesMapId);

        if (! $seriesMap) {
            return;
        }

        if (! $seriesMap->start_date) {
            // If the series map doesn't have a start date, we don't want to update the stats
            return;
        }

        if ($seriesMap->start_date->gte($this->logReceivedAt)) {
            // If the log was before the series map started, we don't want to update the stats
            return;
        }

        if ($this->statistic === 'damage' && $this->victimId) {
            // ADR should not count damage done beyond the 100hp of the victim
            $victimHealth = $seriesMap->players()->where('player_id', $this->victimId)->first()?->pivot?->health ?? 100;
            $this->value = min($this->value, $victimHealth);
        }

        $seriesMap->players()->newPivotQuery()->updateOrInsert([
            'player_id' => $this->playerId,
            'series_map_id' => $this->seriesMapId,
        ], [
            $this->statistic => DB::raw($this->statistic.' '.$this->operator.' '.$this->value),
        ]);
    }
}
