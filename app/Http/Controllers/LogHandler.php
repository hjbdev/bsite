<?php

namespace App\Http\Controllers;

use App\Enums\SeriesMapStatus;
use App\Enums\SeriesStatus;
use App\Models\Map;
use App\Models\Series;
use App\Models\SeriesMap;
use CSLog\CS2\Models\MatchEnd;
use CSLog\CS2\Models\MatchStatus;
use CSLog\CS2\Patterns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LogHandler extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // logger(json_encode($request->headers->all()));
        try {
            if (!$serverInstanceToken = $request->header('x-server-instance-token')) {
                abort(403);
            }

            $series = null;

            if (Cache::has('series-' . $serverInstanceToken)) {
                $series = Cache::get('series-' . $serverInstanceToken);
            } else {
                // Need to try and find the series by players featured in the game
                // try {
                //     $series = Series::where('secret', $serverInstanceToken)->firstOrFail();
                // } catch (\Exception $e) {
                //     logger('Couldnt find series with secret: ' . $serverInstanceToken);
                //     throw $e;
                // }
                // Cache::put('series-' . $series->server_token, $series, 60);
            }

            $maps = null;

            if (Cache::has('maps')) {
                $maps = Cache::get('maps');
            } else {
                $maps = Map::get();
                Cache::put('maps', $maps, Map::CACHE_TTL);
            }

            $rawLog = file_get_contents('php://input');

            str($rawLog)->split("~\R~u")->each(function ($rawLogLine) use ($series, $serverInstanceToken, $maps) {
                $log = Patterns::match($rawLogLine);

                if (!$log) {
                    // logger('NO LOG--' . $rawLogLine);
                    return;
                }

                if (!$series) {
                    logger('no series');
                    // Firstly figure out if this is a log that contains a player
                    // If it does, we can try and find the series by the player
                    if ($logSteamId = ($log?->steamId ?? $log?->killerSteamId ?? $log?->victimSteamId ?? $log?->throwerSteamId ?? null)) {

                        $logSteamId = str($logSteamId)->replace('[', '')->replace(']', '')->__toString();

                        $series = Series::whereIn('status', [SeriesStatus::UPCOMING, SeriesStatus::ONGOING])
                            ->where(function ($query) use ($logSteamId) {
                                $query->whereHas('teamA', function ($teamAQuery) use ($logSteamId) {
                                    $teamAQuery->whereHas('players', function ($teamAPlayerQuery) use ($logSteamId) {
                                        $teamAPlayerQuery->where('steam_id3', $logSteamId);
                                    });
                                });
                                $query->orWhereHas('teamB', function ($teamBQuery) use ($logSteamId) {
                                    $teamBQuery->whereHas('players', function ($teamBPlayerQuery) use ($logSteamId) {
                                        $teamBPlayerQuery->where('steam_id3', $logSteamId);
                                    });
                                });
                            })->first();

                        if (!$series) {
                            logger('still no series');
                            return;
                        }

                        $series->server_token = $serverInstanceToken;
                        $series->save();
                        Cache::put('series-' . $series->server_token, $series, Series::CACHE_TTL);
                    }
                }

                // logger(json_encode($log));
                $series?->logs()->create([
                    'type' => $log->type,
                    'data' => (array) $log,
                ]);

                if ($log instanceof MatchStatus && $series) {
                    $series->rounds_played = $log->roundsPlayed;
                    $currentMap = $maps->firstWhere('name', $log->map);

                    if ($series->seriesMaps()->where('map_id', $currentMap->id)->doesntExist()) {
                        // If we've started a new map, and the other one hasn't finished, mark it as cancelled.
                        $series->seriesMaps()->where('status', SeriesMapStatus::ONGOING)->update([
                            'status' => SeriesMapStatus::CANCELLED,
                        ]);
                    }

                    $seriesMap = $series->seriesMaps()->updateOrCreate([
                        'map_id' => $currentMap->id,
                    ], [
                        'team_a_score' => $log->scoreA,
                        'team_b_score' => $log->scoreB,
                        'start_date'   => $log->roundsPlayed > -1 ? now() : null,
                        'status'       => $log->roundsPlayed > -1 ? SeriesMapStatus::ONGOING : SeriesMapStatus::UPCOMING,
                    ]);


                    $series->current_series_map_id = $seriesMap->id;
                    $series->save();

                    Cache::put('series-map-' . $seriesMap->id, $seriesMap, Series::CACHE_TTL);
                    Cache::put('series-' . $series->server_token, $series, Series::CACHE_TTL);
                }

                if ($log instanceof MatchEnd && $series) {
                    if ($series->current_series_map_id) {
                        Cache::get('series-map-' . $series->current_series_map_id, function () use ($series) {
                            return SeriesMap::find($series->current_series_map_id);
                        })->update([
                            'status' => SeriesMapStatus::FINISHED,
                        ]);
                        $series->current_series_map_id = null;
                        $series->save();
                        Cache::put('series-' . $series->server_token, $series, Series::CACHE_TTL);
                    }
                }
            });
        } catch (\Throwable $e) {
            logger($e->getMessage());
            logger($e->getTraceAsString());
        } finally {
            abort(200);
            // No matter what happens, we want to return a 200
            // Otherwise the server will keep trying to send the log
        }
    }
}
