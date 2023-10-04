<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Series extends Model
{
    use HasFactory;

    public const CACHE_TTL = 240;

    protected $hidden = ['secret'];

    protected $fillable = [
        'event_id',
        'team_a_id',
        'team_b_id',
        'team_a_score',
        'team_b_score',
        'type',
        'status',
        'start_date',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($series) {
            // $series->secret = str()->random(32);
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function seriesMaps(): HasMany
    {
        return $this->hasMany(SeriesMap::class);
    }

    public function teamA(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_a_id');
    }

    public function teamB(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_b_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }
}
