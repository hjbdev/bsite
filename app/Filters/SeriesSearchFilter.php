<?php

namespace App\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class SeriesSearchFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where(function ($q) use ($value) {
            $q->whereHas('teamA', function ($q) use ($value) {
                $q->where('name', 'like', "%{$value}%");
            });
            $q->orWhereHas('teamB', function ($q) use ($value) {
                $q->where('name', 'like', "%{$value}%");
            });
        });
    }
}
