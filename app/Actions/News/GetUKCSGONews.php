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

                $srcset = $img->match('/srcset=".*?"/');

                if ($srcset) {
                    $srcset = $srcset->replace('srcset="', '');
                    $srcset = $srcset->replace('"', '');
                    $srcset = $srcset->explode(', ');

                    $srcset = $srcset->map(function ($src) {
                        $src = str($src);
                        $src = $src->explode(' ');
                        return [
                            'url' => $src[0],
                            'width' => str($src[1])->replace('w', '')->toInteger(),
                        ];
                    });

                    $src = $srcset->where('width', '<', 600)->sortByDesc('width')->first()['url'];
                    $img = $img->replaceMatches('/src=".*?"/', "src=\"{$src}\"");
                    $img = $img->replaceMatches('/sizes=".*?"/', '');
                    $img = $img->replaceMatches('/decoding=".*?"/', '');
                    $img = $img->replaceMatches('/srcset=".*?"/', '');
                }

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

