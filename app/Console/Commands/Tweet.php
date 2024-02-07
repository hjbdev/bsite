<?php

namespace App\Console\Commands;

use App\Models\Series;
use Illuminate\Console\Command;

class Tweet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:tweet {seriesId}';

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
        $series = Series::with('teamA', 'teamB', 'event')
            ->findOrFail($this->argument('seriesId'));

        $series->socialPosts()->create([
            'content' => $this->generatePostContent($series),
            'platform' => 'twitter',
            'type' => 'upcoming_series_reminder_2h',
            'link_to_post' => route('matches.show.seo', [
                'match' => $series->id,
                'slug' => $series->slug,
            ]),
            'generator_url' => 'https://bsite.uk/generators/upcoming-series/'.$series->id,
        ]);
    }

    private function generatePostContent(Series $series): string
    {
        $post = "⚔️ Upcoming Match \n\n";

        if ($series->event) {
            $post .= '🏟️ '.$series->event->name."\n";
        }

        $post .= '⌚ '.$series->start_date->format('g:i a')."\n";
        $post .= '⚔️ ';
        if ($series->teamA && $series->teamA->twitter_handle) {
            $post .= '@'.$series->teamA->twitter_handle;
        } else {
            $post .= $series->teamA->name;
        }

        $post .= ' vs ';

        if ($series->teamB && $series->teamB->twitter_handle) {
            $post .= '@'.$series->teamB->twitter_handle;
        } elseif ($series->teamB) {
            $post .= $series->teamB->name;
        } else {
            $post .= $series->team_b_name;
        }

        return $post;
    }
}
