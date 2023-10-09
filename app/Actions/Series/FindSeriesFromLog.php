<?php

namespace App\Actions\Series;

use App\Enums\SeriesStatus;
use App\Models\Series;
use CSLog\Model;

class FindSeriesFromLog
{
    public function execute(Model $log): ?Series
    {
        // Firstly figure out if this is a log that contains a player
        // If it does, we can try and find the series by the player
        if ($logSteamId = ($log?->steamId ?? $log?->killerSteamId ?? $log?->victimSteamId ?? $log?->throwerSteamId ?? null)) {
            $series = Series::whereIn('status', [SeriesStatus::UPCOMING, SeriesStatus::ONGOING])
                ->where(function ($query) use ($logSteamId) {
                    $query->whereHas('teamA', function ($teamAQuery) use ($logSteamId) {
                        $teamAQuery->whereHas('players', function ($teamAPlayerQuery) use ($logSteamId) {
                            $teamAPlayerQuery->whereNull('player_team.end_date');
                            $teamAPlayerQuery->where('steam_id3', $logSteamId);
                        });
                    });
                    $query->orWhereHas('teamB', function ($teamBQuery) use ($logSteamId) {
                        $teamBQuery->whereHas('players', function ($teamBPlayerQuery) use ($logSteamId) {
                            $teamBPlayerQuery->whereNull('player_team.end_date');
                            $teamBPlayerQuery->where('steam_id3', $logSteamId);
                        });
                    });
                })->with('event')->first();

            if (! $series) {
                return null;
            }

            return $series;
        }

        return null;
    }
}
