<?php

namespace App\Http\Controllers;

use App\Enums\SeriesMapStatus;
use App\Enums\SeriesStatus;
use App\Models\Map;
use App\Models\Player;
use App\Models\Series;
use App\Models\SeriesMap;
use App\Models\Team;
use CSLog\CS2\Models\Kill;
use CSLog\CS2\Models\MatchEnd;
use CSLog\CS2\Models\MatchStatus;
use CSLog\CS2\Models\SwitchTeam;
use CSLog\CS2\Models\TeamScored;
use CSLog\CS2\Patterns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class LogHandler extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            if (!$serverInstanceToken = $request->header('x-server-instance-token')) {
                logger('No Instance token ' . $request->ip());
                abort(403);
            }

            $series = null;

            if (Cache::has('series-' . $serverInstanceToken)) {
                $series = Cache::get('series-' . $serverInstanceToken);
            }

            $maps = null;

            if (Cache::has('maps')) {
                $maps = Cache::get('maps');
            } else {
                $maps = Map::get();
                Cache::put('maps', $maps, Map::CACHE_TTL);
            }

            $rawLog = $request->getContent();

            str($rawLog)->split("~\R~u")->each(function ($rawLogLine) use ($series, $serverInstanceToken, $maps) {
                $log = Patterns::match($rawLogLine);

                if (!$log) {
                    // logger('NO LOG--' . $rawLogLine);
                    return;
                }

                // logger('Log Parsed');

                if (!$series) {
                    logger('no series');
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
                            })->first();

                        if (!$series) {
                            logger('still no series');
                            return;
                        }

                        $series->server_token = $serverInstanceToken;
                        $series->save();
                        Cache::put('series-' . $series->server_token, $series, Series::CACHE_TTL);
                    } else {
                        logger('still no series');
                        return;
                    }
                }

                // logger(json_encode($log));
                $series?->logs()->create([
                    'type' => $log->type,
                    'data' => (array) $log,
                ]);

                if ($log instanceof Kill && $series) {
                    if (!$series->terrorist_team_id || $series->ct_team_id) {
                        $team = Team::whereHas('players', function ($query) use ($log) {
                            $query->where('players.steam_id3', $log->killerSteamId);
                            $query->whereNull('player_team.end_date');
                        })->first(['id']);

                        if ($team) {
                            // We can now determine, based on this log, which team is which
                            if ($team->id !== $series->team_a_id && $team->id !== $series->team_b_id) {
                                // this is a problem.
                                logger('We\'ve matched a player to a team that is not participating in this series.');
                            } else {
                                $isTeamA = $team->id === $series->team_a_id;

                                if ($log->killerTeam === 'TERRORIST') {
                                    // Associate the found team with T side
                                    $series->terroristTeam()->associate($team);
                                    // Associate the other team with the other side (CT)
                                    $series->ctTeam()->associate($isTeamA ? $series->team_b_id : $series->team_a_id);
                                } else {
                                    $series->ctTeam()->associate($team);
                                    $series->terroristTeam()->associate($isTeamA ? $series->team_b_id : $series->team_a_id);
                                }
                            }

                            $series->save();
                            Cache::put('series-' . $series->server_token, $series, Series::CACHE_TTL);
                        }
                    }
                }

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
                        'status' => $log->roundsPlayed > -1 ? SeriesMapStatus::ONGOING : SeriesMapStatus::UPCOMING,
                    ]);

                    if ($seriesMap->wasRecentlyCreated) {
                        $seriesMap->start_date = $log->roundsPlayed > -1 ? now() : null;
                        $seriesMap->save();
                    }

                    if ($seriesMap->status === SeriesMapStatus::ONGOING) {
                        $series->status = SeriesStatus::ONGOING;
                    }

                    $series->current_series_map_id = $seriesMap->id;
                    $series->save();

                    Cache::put('series-map-' . $seriesMap->id, $seriesMap, Series::CACHE_TTL);
                    Cache::put('series-' . $series->server_token, $series, Series::CACHE_TTL);
                }

                if ($log instanceof SwitchTeam && $series) {
                    // determine which team is which
                    // $player = Player::where('steam_id3', $log->steamId)->first();
                    $team = Team::whereHas('players', function ($query) use ($log) {
                        $query->where('players.steam_id3', $log->steamId);
                        $query->whereNull('player_team.end_date');
                    })->first(['id']);

                    if ($team) {
                        // We can now determine, based on this log, which team is which
                        if ($team->id !== $series->team_a_id && $team->id !== $series->team_b_id) {
                            // this is a problem.
                            logger('We\'ve matched a player to a team that is not participating in this series.');
                        } else {
                            $isTeamA = $team->id === $series->team_a_id;

                            if ($log->newTeam === 'TERRORIST') {
                                // Associate the found team with T side
                                $series->terroristTeam()->associate($team);
                                // Associate the other team with the other side (CT)
                                $series->ctTeam()->associate($isTeamA ? $series->team_b_id : $series->team_a_id);
                            } else {
                                $series->ctTeam()->associate($team);
                                $series->terroristTeam()->associate($isTeamA ? $series->team_b_id : $series->team_a_id);
                            }
                        }

                        $series->save();
                        Cache::put('series-' . $series->server_token, $series, Series::CACHE_TTL);
                    }
                }

                if ($log instanceof TeamScored) {
                    if ($series->current_series_map_id) {
                        $seriesMap = Cache::remember('series-map-' . $series->current_series_map_id, Series::CACHE_TTL, function () use ($series) {
                            return SeriesMap::find($series->current_series_map_id);
                        });

                        if ($log->team === 'TERRORIST') {
                            // figure out which team is T
                            $scoreKey = $series->terrorist_team_id === $series->team_a_id ? 'team_a_score' : 'team_b_score';
                            $seriesMap->{$scoreKey} = $log->score;
                            logger('log team terrorist ' . $scoreKey . ' score: ' . $log->score . ' series T team id: ' . $series->terrorist_team_id . ' series team A ID: ' . $series->team_a_id);
                        } else {
                            $scoreKey = $series->ct_team_id === $series->team_a_id ? 'team_a_score' : 'team_b_score';
                            $seriesMap->{$scoreKey} = $log->score;
                            logger('log team ct ' . $scoreKey . ' ' . $log->score . ' ' . $series->ct_team_id . ' ' . $series->team_a_id);
                        }

                        $seriesMap->save();
                        Cache::put('series-map-' . $series->current_series_map_id, $seriesMap, Series::CACHE_TTL);
                    }
                }

                if ($log instanceof MatchEnd && $series) {
                    if ($series->current_series_map_id) {
                        Cache::remember('series-map-' . $series->current_series_map_id, Series::CACHE_TTL, function () use ($series) {
                            return SeriesMap::find($series->current_series_map_id);
                        })->update([
                            'status' => SeriesMapStatus::FINISHED,
                        ]);

                        Cache::forget('series-map-' . $series->current_series_map_id);

                        $series->current_series_map_id = null;

                        if (!$series->remainingMaps()) {
                            $series->status = SeriesStatus::FINISHED;
                            $series->save();
                            Cache::forget('series-' . $series->server_token);
                        } else {
                            $series->save();
                            Cache::put('series-' . $series->server_token, $series, Series::CACHE_TTL);
                        }
                    }
                }
            });
        } catch (\Throwable $e) {
            logger($e->getMessage());
            logger($e->getTraceAsString());
        } finally {
            return response('', 200);
            // No matter what happens, we want to return a 200
            // Otherwise the server will keep trying to send the log
        }
    }
}
