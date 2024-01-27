<?php

namespace App\Jobs\SocialPosts;

use App\Models\SocialPost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Noweh\TwitterApi\Client;
use Spatie\Browsershot\Browsershot;

class Post implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public SocialPost $socialPost
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->socialPost->platform === 'twitter') {
            $base64Data = Browsershot::url($this->socialPost->generator_url)
                ->windowSize(1600, 900)
                ->setChromePath('/usr/bin/chromium-browser')
                ->base64Screenshot();

            $client = new Client([
                'account_id' => env('TWITTER_ACCOUNT_ID'),
                'consumer_key' => env('TWITTER_CONSUMER_KEY'),
                'consumer_secret' => env('TWITTER_CONSUMER_SECRET'),
                'access_token' => env('TWITTER_ACCESS_TOKEN'),
                'access_token_secret' => env('TWITTER_ACCESS_TOKEN_SECRET'),
                'bearer_token' => env('TWITTER_BEARER_TOKEN'),
            ]);

            $media = $client->uploadMedia()->upload($base64Data);

            $tweet = $client->tweet()->create()->performRequest([
                'text' => $this->socialPost->content,
                'media' => [
                    'media_ids' => [
                        (string) $media['media_id'],
                    ],
                ],
            ]);

            if ($this->socialPost->link_to_post && $tweet->data->id) {
                $client->tweet()->create()->performRequest([
                    'reply' => ['in_reply_to_tweet_id' =>  $tweet->data->id],
                    'text' => '🔗 ' . $this->socialPost->link_to_post,
                ]);
            }

            $this->socialPost->update([
                'platform_post_id' => $tweet->data->id,
                'platform_post_url' => 'https://twitter.com/bsiteuk/status/' . $tweet->data->id,
            ]);
        }
    }
}
