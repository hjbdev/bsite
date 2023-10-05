<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use SteamID;

class Player extends Model
{
    use HasFactory;

    public static function boot(): void
    {
        parent::boot();

        $convertSteamId = function (Player $player): void {
            try {
                $steamId = new SteamID($player->steam_id64);
                $player->steam_id3 = $steamId->RenderSteam3();
            } catch (\Exception $e) {
                $player->steam_id3 = null;
                logger("Failed to convert SteamID for " . $player->name);
            }
        };

        static::creating($convertSteamId);
        static::updating($convertSteamId);
    }

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
