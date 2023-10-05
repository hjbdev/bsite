<?php

namespace App\Models;

use App\Enums\SeriesMapStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeriesMap extends Model
{
    use HasFactory;

    protected $fillable = ['team_a_score', 'team_b_score', 'series_id', 'map_id', 'start_date', 'status'];

    protected $casts = [
        'status' => SeriesMapStatus::class,
    ];

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function map(): BelongsTo
    {
        return $this->belongsTo(Map::class);
    }
}
