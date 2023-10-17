<?php

namespace App\Actions\Series;

use App\Models\Series;
use Illuminate\Support\Facades\Cache;

class GetCachedSeries
{
    public function execute($id, $cacheKey = null): Series
    {
        if (! $cacheKey) {
            $cacheKey = 'series-'.$id;
        }

        return Cache::remember(
            $cacheKey,
            Series::CACHE_TTL,
            fn () => Series::findOrFail($id)
        );
    }
}
