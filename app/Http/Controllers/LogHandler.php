<?php

namespace App\Http\Controllers;

use App\Actions\Players\GetCachedPlayerWithSteamId3;
use App\Actions\Series\FindSeriesFromLog;
use App\Actions\SeriesMap\GetCachedSeriesMap;
use App\Enums\SeriesMapStatus;
use App\Enums\SeriesStatus;
use App\Events\Logs\LogCreated;
use App\Jobs\Logs\BroadcastLogCreated;
use App\Jobs\Players\IncrementPlayerSeriesMapStatistic;
use App\Jobs\Series\RecalculateSeriesScore;
use App\Jobs\SeriesMaps\SetSeriesMapRoundsPlayed;
use App\Jobs\SeriesMaps\UpdateSeriesMapScore;
use App\Models\Log;
use App\Models\Map;
use App\Models\Player;
use App\Models\Series;
use App\Models\SeriesMap;
use App\Models\Team;
use CSLog\CS2\Models\Attack;
use CSLog\CS2\Models\Kill;
use CSLog\CS2\Models\KillAssist;
use CSLog\CS2\Models\MatchEnd;
use CSLog\CS2\Models\MatchStatus;
use CSLog\CS2\Models\SwitchTeam;
use CSLog\CS2\Models\TeamScored;
use CSLog\CS2\Patterns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LogHandler extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            if (! $serverInstanceToken = $request->header('x-server-instance-token')) {
                logger('No Instance token '.$request->ip());
                abort(403);
            }

            $series = null;

            if (Cache::has('series-'.$serverInstanceToken)) {
                $series = Cache::get('series-'.$serverInstanceToken);
            }

            $maps = null;

            if (Cache::has('maps')) {
                $maps = Cache::get('maps');
            } else {
                $maps = Map::get();
                Cache::put('maps', $maps, Map::CACHE_TTL);
            }

            $rawLog = $request->getContent();

            $logReceivedAt = now();

            str($rawLog)->split("~\R~u")->each(function ($rawLogLine) use ($series, $serverInstanceToken, $maps, $logReceivedAt) {
                $log = Patterns::match($rawLogLine);

                if (! $log) {
                    return;
                }

                if (! $series) {
                    logger('no series');
                    $series = app(FindSeriesFromLog::class)->execute($log);

                    if (! $series) {
                        logger('still no series');

                        return;
                    }

                    logger('found series');
                    $series->server_token = $serverInstanceToken;
                    $series->save();
                    Cache::put('series-'.$series->server_token, $series, Series::CACHE_TTL);
                }

                $logModel = $series?->logs()->create([
                    'type' => $log->type,
                    'data' => (array) $log,
                ]);

                if (in_array($logModel->type, Log::BROADCASTABLE_EVENTS)) {
                    // broadcast(new LogCreated($log));
                    dispatch(new BroadcastLogCreated($logModel))->delay($series->event->delay);
                }

                unset($logModel); // I don't want to play with you anymore.

                if ($log instanceof Kill && $series) {
                    if (! ($series->terrorist_team_id || $series->ct_team_id)) {
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
                            Cache::put('series-'.$series->server_token, $series, Series::CACHE_TTL);
                        }
                    }

                    if ($series->current_series_map_id) {
                        $seriesMap = app(GetCachedSeriesMap::class)->execute($series->current_series_map_id);
                        $killer = app(GetCachedPlayerWithSteamId3::class)->execute($log->killerSteamId);
                        $victim = app(GetCachedPlayerWithSteamId3::class)->execute($log->killedSteamId);

                        if ($killer) {
                            // $seriesMap->players()->newPivotQuery()->updateOrInsert([
                            //     'player_id' => $killer->id,
                            //     'series_map_id' => $seriesMap->id,
                            // ], [
                            //     'kills' => DB::raw('kills + 1')
                            // ]);
                            dispatch(new IncrementPlayerSeriesMapStatistic(
                                playerId: $killer->id,
                                seriesMapId: $seriesMap->id,
                                statistic: 'kills',
                                value: 1,
                                logReceivedAt: $logReceivedAt
                            ))->delay($series->event->delay);
                        }

                        if ($victim) {
                            // $seriesMap->players()->newPivotQuery()->updateOrInsert([
                            //     'player_id' => $victim->id,
                            //     'series_map_id' => $seriesMap->id,
                            // ], [
                            //     'deaths' => DB::raw('deaths + 1')
                            // ]);
                            dispatch(new IncrementPlayerSeriesMapStatistic(
                                playerId: $victim->id,
                                seriesMapId: $seriesMap->id,
                                statistic: 'deaths',
                                value: 1,
                                logReceivedAt: $logReceivedAt
                            ))->delay($series->event->delay);
                        }

                        unset($killer);
                        unset($victim);
                    }
                }

                if ($log instanceof Attack && $series && $series->current_series_map_id && ($log->attackerTeam !== $log->victimTeam)) {
                    $seriesMap = app(GetCachedSeriesMap::class)->execute($series->current_series_map_id);
                    $attacker = app(GetCachedPlayerWithSteamId3::class)->execute($log->attackerSteamId);

                    if ($attacker) {
                        // $seriesMap->players()->newPivotQuery()->updateOrInsert([
                        //     'player_id' => $attacker->id,
                        //     'series_map_id' => $seriesMap->id,
                        // ], [
                        //     'damage' => DB::raw('damage + ' . min($log->attackerDamage, 100))
                        // ]);
                        dispatch(new IncrementPlayerSeriesMapStatistic(
                            playerId: $attacker->id,
                            seriesMapId: $seriesMap->id,
                            statistic: 'damage',
                            value: min($log->attackerDamage, 100),
                            logReceivedAt: $logReceivedAt
                        ))->delay($series->event->delay);
                    }

                    unset($attacker);
                }

                if ($log instanceof KillAssist && $series && $series->current_series_map_id && ($log->killedTeam !== $log->assisterTeam)) {
                    $seriesMap = app(GetCachedSeriesMap::class)->execute($series->current_series_map_id);
                    $assister = app(GetCachedPlayerWithSteamId3::class)->execute($log->assisterSteamId);

                    if ($assister) {
                        // $seriesMap->players()->newPivotQuery()->updateOrInsert([
                        //     'player_id' => $assister->id,
                        //     'series_map_id' => $seriesMap->id,
                        // ], [
                        //     'assists' => DB::raw('assists + 1')
                        // ]);
                        dispatch(new IncrementPlayerSeriesMapStatistic(
                            playerId: $assister->id,
                            seriesMapId: $seriesMap->id,
                            statistic: 'assists',
                            value: 1,
                            logReceivedAt: $logReceivedAt
                        ))->delay($series->event->delay);
                    }

                    unset($assister);
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

                    if ($seriesMap->wasRecentlyCreated && $log->roundsPlayed > -1) {
                        // if seriesMap is created in advance THIS DOESNT RUN -- FIX
                        $seriesMap->start_date = $logReceivedAt;
                        // reset stats
                        $seriesMap->players()->detach();
                        $seriesMap->save();

                        if (! $series->start_date) {
                            $series->start_date = $logReceivedAt;
                        }
                    }

                    if ($seriesMap->status === SeriesMapStatus::ONGOING) {
                        $series->status = SeriesStatus::ONGOING;
                    }

                    dispatch(new SetSeriesMapRoundsPlayed($seriesMap->id, $log->roundsPlayed, $logReceivedAt))->delay($series->event->delay);

                    $series->current_series_map_id = $seriesMap->id;
                    $series->save();

                    Cache::put('series-map-'.$seriesMap->id, $seriesMap, Series::CACHE_TTL);
                    Cache::put('series-'.$series->server_token, $series, Series::CACHE_TTL);
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
                        Cache::put('series-'.$series->server_token, $series, Series::CACHE_TTL);
                    }
                }

                if ($log instanceof TeamScored) {
                    if ($series->current_series_map_id) {
                        $seriesMap = app(GetCachedSeriesMap::class)->execute($series->current_series_map_id);
                        // $seriesMap = Cache::remember('series-map-' . $series->current_series_map_id, Series::CACHE_TTL, function () use ($series) {
                        //     return SeriesMap::find($series->current_series_map_id);
                        // });

                        if ($log->team === 'TERRORIST') {
                            // figure out which team is T
                            $scoreKey = $series->terrorist_team_id === $series->team_a_id ? 'team_a_score' : 'team_b_score';
                            dispatch(new UpdateSeriesMapScore($seriesMap->id, $scoreKey, $log->score))->delay($series->event->delay);
                            // logger('log team terrorist ' . $scoreKey . ' score: ' . $log->score . ' series T team id: ' . $series->terrorist_team_id . ' series team A ID: ' . $series->team_a_id);
                        } else {
                            $scoreKey = $series->ct_team_id === $series->team_a_id ? 'team_a_score' : 'team_b_score';
                            dispatch(new UpdateSeriesMapScore($seriesMap->id, $scoreKey, $log->score))->delay($series->event->delay);
                            // logger('log team ct ' . $scoreKey . ' ' . $log->score . ' ' . $series->ct_team_id . ' ' . $series->team_a_id);
                        }
                    }
                }

                if ($log instanceof MatchEnd && $series) {
                    if ($series->current_series_map_id) {
                        app(GetCachedSeriesMap::class)->execute($series->current_series_map_id)->update([
                            'status' => SeriesMapStatus::FINISHED,
                        ]);

                        Cache::forget('series-map-'.$series->current_series_map_id);

                        $series->current_series_map_id = null;

                        // This should be fine
                        dispatch(new RecalculateSeriesScore($series->id))->delay($series->event->delay);
                        // This is just in case.
                        dispatch(new RecalculateSeriesScore($series->id))->delay($series->event->delay + 120);

                        if (! $series->remainingMaps()) {
                            $series->status = SeriesStatus::FINISHED;
                            $series->save();
                            Cache::forget('series-'.$series->server_token);
                        } else {
                            $series->save();
                            Cache::put('series-'.$series->server_token, $series, Series::CACHE_TTL);
                        }
                    } else {
                        logger("We received a MatchEnd event, with series {$series->id}, but there is no current map.");
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
