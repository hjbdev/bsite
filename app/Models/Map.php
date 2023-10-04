<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Map extends Model
{
    use HasFactory;

    public const CACHE_TTL = 60 * 60 * 24 * 7;

    protected $fillable = [
        'name',
        'title',
    ];

    public function seriesMaps(): HasMany
    {
        return $this->hasMany(SeriesMap::class);
    }
}
