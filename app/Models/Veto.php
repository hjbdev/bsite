<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Veto extends Model
{
    use HasFactory;

    protected $fillable = [
        'series_id',
        'map_id',
        'team_id',
        'type',
    ];

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function map(): BelongsTo
    {
        return $this->belongsTo(Map::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
