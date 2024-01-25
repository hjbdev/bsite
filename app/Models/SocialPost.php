<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Noweh\TwitterApi\Client;
use Spatie\Browsershot\Browsershot;

class SocialPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'platform',
        'platform_post_id',
        'platform_post_url',
        'generator_url',
        'type',
        'link_to_post',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function (SocialPost $model) {
            if ($model->platform === 'twitter') {
                $base64Data = Browsershot::url($model->generator_url)
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
                    'text' => $model->content,
                    'media' => [
                        'media_ids' => [
                            (string) $media['media_id'],
                        ],
                    ],
                ]);

                if ($model->link_to_post && $tweet->data->id) {
                    $client->tweet()->create()->performRequest([
                        'reply' => [ 'in_reply_to_tweet_id' =>  $tweet->data->id ],
                        'text' => '🔗 ' . $model->link_to_post,
                    ]);
                }

                $model->update([
                    'platform_post_id' => $tweet->data->id,
                    'platform_post_url' => 'https://twitter.com/bsiteuk/status/' . $tweet->data->id,
                ]);
            }
        });
    }

    public function postable(): MorphTo
    {
        return $this->morphTo();
    }
}
