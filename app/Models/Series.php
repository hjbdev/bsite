<?php

namespace App\Models;

use App\Enums\SeriesMapStatus;
use App\Enums\SeriesStatus;
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

    protected $casts = [
        'status' => SeriesStatus::class,
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($series) {
            $series->slug = str($series->teamA->name)->slug() . '-vs-' . str($series->teamB->name)->slug() . '-' . str($series->event->name)->slug();
        });
        
        static::updating(function ($series) {
            $series->slug = str($series->teamA->name)->slug() . '-vs-' . str($series->teamB->name)->slug() . '-' . str($series->event->name)->slug();
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

    public function currentSeriesMap(): BelongsTo
    {
        return $this->belongsTo(SeriesMap::class, 'current_series_map_id');
    }

    public function remainingMaps(): int
    {
        $mapCount = intval((string) str($this->type)->after('bo'));
        return $mapCount - $this->seriesMaps()->whereIn('status', [SeriesMapStatus::FINISHED, SeriesMapStatus::ONGOING])->count();
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
