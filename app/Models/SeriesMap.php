<?php

namespace App\Models;

use App\Enums\SeriesMapStatus;
use App\Events\SeriesMaps\SeriesMapUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SeriesMap extends Model
{
    use HasFactory;

    protected $fillable = ['team_a_score', 'team_b_score', 'series_id', 'map_id', 'start_date', 'status'];

    protected $casts = [
        'status' => SeriesMapStatus::class,
        'start_date' => 'datetime',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::updated(function ($seriesMap) {
            broadcast(new SeriesMapUpdated($seriesMap->id, $seriesMap->series_id));
        });
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function map(): BelongsTo
    {
        return $this->belongsTo(Map::class);
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class)->withPivot(['kills', 'assists', 'deaths', 'damage', 'traded', 'kast', 'rating']);
    }
}
