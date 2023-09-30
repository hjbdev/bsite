<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Map extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
    ];

    public function seriesMaps(): HasMany
    {
        return $this->hasMany(SeriesMap::class);
    }
}
