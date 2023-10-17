<?php

namespace App\Actions\News;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GetUKCSGONews
{
    public function execute()
    {
        return Cache::remember('ukcsgo-news', 60 * 60 * 24, function () {
            $articles = collect();
            $response = Http::get('https://ukcsgo.com/feed/');

            $xml = simplexml_load_string($response->body());

            foreach ($xml->channel->item as $item) {
                // Parse HTML
                $img = str($item->description[0]->__toString())->match('/<img.*\/>/mU');


                $img = $img->replaceMatches('/width="[0-9]*"/', '');
                $img = $img->replaceMatches('/height="[0-9]*"/', '');
                $img = $img->replaceMatches('/class="[a-zA-Z0-9\-\s]*"/', '');
                $img = $img->replaceMatches('/style="[a-zA-Z0-9\-\s:%;]*"/', '');
                $img = $img->replace('link_thumbnail="1"', '');
                $img = $img->squish();

                $articles->push([
                    'title' => $item->title->__toString(),
                    'url' => $item->link->__toString(),
                    'image' => $img->__toString(),
                ]);
            }

            return $articles;
        });
    }
}
