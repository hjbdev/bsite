<?php

namespace App\Jobs\Series;

use App\Enums\SeriesMapStatus;
use App\Models\Series;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecalculateSeriesScore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $seriesId
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $series = Series::findOrFail($this->seriesId);

        $series->team_a_score = 0;
        $series->team_b_score = 0;

        foreach ($series->seriesMaps as $seriesMap) {
            if ($seriesMap->status === SeriesMapStatus::FINISHED) {
                if ($seriesMap->team_a_score > $seriesMap->team_b_score) {
                    $series->team_a_score++;
                } else {
                    $series->team_b_score++;
                }
            }
        }

        $series->update([
            'team_a_score' => $series->team_a_score,
            'team_b_score' => $series->team_b_score,
        ]);
    }
}
