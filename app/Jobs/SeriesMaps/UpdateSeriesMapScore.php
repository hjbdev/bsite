<?php

namespace App\Jobs\SeriesMaps;

use App\Models\Series;
use App\Models\SeriesMap;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class UpdateSeriesMapScore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $seriesMapId,
        public string $scoreKey,
        public int $score
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $seriesMap = SeriesMap::findOrFail($this->seriesMapId);
        $seriesMap->{$this->scoreKey} = $this->score;
        $seriesMap->save();
        
        Cache::put('series-map-' . $seriesMap->id, $seriesMap, Series::CACHE_TTL);
    }
}
