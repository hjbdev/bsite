<?php

namespace App\Console\Commands;

use App\Models\Series;
use App\Models\SocialPost;
use Illuminate\Console\Command;

class SendUpcomingSeriesSocialPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-upcoming-series-social-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends social posts for upcoming series.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $series = \App\Models\Series::query()
            ->with('teamA', 'teamB', 'event')
            ->where('status', \App\Enums\SeriesStatus::UPCOMING)
            ->where('start_date', '<=', now()->addHours(2))
            ->where('start_date', '>=', now())
            ->whereDoesntHaveMorph('socialPosts', SocialPost::class, function ($query) {
                $query->where('platform', 'twitter');
                $query->where('type', 'upcoming_series_reminder_2h');
            })
            ->chunk(20, function ($seriesList) {
                $seriesList->each(function ($series) {
                    $series->socialPosts()->create([
                        'content' => $this->generatePostContent($series),
                        'platform' => 'twitter',
                        'type' => 'upcoming_series_reminder_2h',
                        'link_to_post' => route('matches.show.seo', [
                            'match' => $series->id,
                            'slug' => $series->slug,
                        ]),
                        'generator_url' => 'https://bsite.uk/generators/upcoming-series/' . $series->id,
                    ]);
                });
            });
    }

    private function generatePostContent(Series $series): string
    {
        $post = "⚔️ Upcoming Match \n\n";

        if ($series->event) {
            $post .= "🏟️ " . $series->event->name . "\n";
        }

        $post .= "⌚ " . $series->start_date->format('g:i a') . "\n";
        $post .= "⚔️";
        if ($series->teamA && $series->teamA->twitter_handle) {
            $post .= "@" . $series->teamA->twitter_handle;
        } else {
            $post .= $series->teamA->name;
        }

        $post .= " vs ";

        if ($series->teamB && $series->teamB->twitter_handle) {
            $post .= "@" . $series->teamB->twitter_handle;
        } else if($series->teamB) {
            $post .= $series->teamB->name;
        } else {
            $post .= $series->team_b_name;
        }

        return $post;
    }
}
