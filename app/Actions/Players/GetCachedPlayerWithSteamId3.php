<?php

namespace App\Actions\Players;

use App\Models\Player;
use Illuminate\Support\Facades\Cache;

class GetCachedPlayerWithSteamId3
{
    public function execute(string $steamId3)
    {
        return Cache::remember('player-' . $steamId3, Player::CACHE_TTL, function () use ($steamId3) {
            return Player::where('steam_id3', $steamId3)->first();
        });
    }
}
