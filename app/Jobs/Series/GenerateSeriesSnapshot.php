<?php

namespace App\Jobs\Series;

use App\Events\Series\SeriesSnapshot;
use App\Models\Log;
use App\Models\Series;
use App\Support\GameState\CS2GameState;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;

class GenerateSeriesSnapshot implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.w
     */
    public function __construct(
        public int $seriesId,
    ) {
        //
    }

    public $uniqueFor = 1;

    public function uniqueId(): string
    {
        return "series-snapshot" . $this->seriesId;
    }

    // public function middleware(): array
    // {
    //     return [(new RateLimited('series-snapshots'))->dontRelease()];
    // }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $gameState = new CS2GameState(Log::where('series_id', $this->seriesId)->get());
        broadcast(new SeriesSnapshot($this->seriesId, $gameState->get()));
    }
}
