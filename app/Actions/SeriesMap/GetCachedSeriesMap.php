<?php

namespace App\Actions\SeriesMap;

use App\Models\Series;
use App\Models\SeriesMap;
use Illuminate\Support\Facades\Cache;

class GetCachedSeriesMap
{
    public function execute($id, $cacheKey = null): SeriesMap
    {
        if (!$cacheKey) {
            $cacheKey = 'series-map-' . $id;
        }

        return Cache::remember(
            $cacheKey,
            Series::CACHE_TTL,
            fn () => SeriesMap::firstOrFail($id)
        );
    }
}
