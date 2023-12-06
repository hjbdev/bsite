<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Noweh\TwitterApi\Client;
use Spatie\Browsershot\Browsershot;

class Tweet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:tweet';

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
        $base64Data = Browsershot::url('https://bsite.uk/generators/upcoming-series/10')->setChromePath('/usr/bin/chromium-browser')->base64Screenshot();

        $client = new Client([
            'account_id' => env('TWITTER_ACCOUNT_ID'),
            'consumer_key' => env('TWITTER_CONSUMER_KEY'),
            'consumer_secret' => env('TWITTER_CONSUMER_SECRET'),
            'access_token' => env('TWITTER_ACCESS_TOKEN'),
            'access_token_secret' => env('TWITTER_ACCESS_TOKEN_SECRET'),
            'bearer_token' => env('TWITTER_BEARER_TOKEN'),
        ]);

        $media = $client->uploadMedia()->upload($base64Data);

        $return = $client->tweet()->create()->performRequest([
            'text' => 'Test Tweet... ',
            "media" => [
                "media_ids" => [
                    (string) $media["media_id"]
                ]
            ]
        ]);

        dd($return);
    }
}
