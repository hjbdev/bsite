<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Player extends Model
{
    use HasFactory;

    // public function logs(): HasMany
    // {
    //     return $this->hasMany(Log::class);
    // }

    public function seriesMaps(): BelongsToMany
    {
        return $this->belongsTo(SeriesMap::class)->using(PlayerSeriesMap::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->withPivot('start_date', 'end_date', 'substitute');
    }
}
