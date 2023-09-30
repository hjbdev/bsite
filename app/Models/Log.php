<?php

namespace App\Models;

use App\Events\Logs\LogCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

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
            broadcast(new LogCreated($log));
        });
    }
}
