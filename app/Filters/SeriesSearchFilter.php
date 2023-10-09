<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

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
