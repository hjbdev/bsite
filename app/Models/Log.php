<?php

namespace App\Models;

use App\Events\Logs\LogCreated;
use App\Jobs\Series\GenerateSeriesSnapshot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    public const BROADCASTABLE_EVENTS = [
        'Kill', 'RoundEnd', 'MatchStatus', 'BombPlanting', 'Blinded', 'BombDefusing'
    ];

    protected $fillable = [
        'series_id',
        'player_id',
        'attacker_id',
        'victim_id',
        'type',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function (Log $log) {
            if (in_array($log->type, self::BROADCASTABLE_EVENTS)) {
                broadcast(new LogCreated($log));
            }
            dispatch(new GenerateSeriesSnapshot($log->series_id));
        });
    }
}
