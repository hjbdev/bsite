<?php

namespace App\Support\GameState;

use App\Models\Log;
use App\Models\Map;
use App\Models\Player;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CS2GameState
{
    public const CACHE_TTL = 60 * 30;

    public ?string $currentMap = null;
    public int $roundsPlayed = 0;
    public array $maps = [];
    public array $players = [];

    // Team A = 1; Team 1 Starts CT

    public function getMappedPlayers(): array
    {
        $players = collect($this->players)->map(function ($player, $key) {
            return [...$player, 'steamId' => $key];
        })->values();

        return $players->toArray();
    }

    public function __construct(protected int $seriesId)
    {
        // $this->initialize();

        try {
            $this->analyseLogs();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            // $this->tearDown();
        }
    }

    public function get()
    {
        $steamIds = collect([]);

        logger(json_encode($this->maps));

        foreach ($this->maps as $map => $mapData) {
            if ($mapData['players'] ?? false) {
                foreach ($mapData['players'] as $player) {
                    $steamIds->push($player['steamId']);
                }
            }
        }

        $dbPlayers = Player::whereIn('steam_id3', $steamIds)->get(['id', 'name', 'steam_id3'])->keyBy('steam_id3')->toArray();

        $maps = $this->maps;

        foreach ($maps as $map => $mapData) {
            foreach ($mapData['players'] as $key => $player) {
                if ($dbPlayers[$player['steamId']] ?? false) {
                    $maps[$map]['players'][$key]['name'] = $dbPlayers[$player['steamId']]['name'];
                }
            }
        }

        $players = collect();

        foreach ($players as $playerIndex => $player) {
            if ($dbPlayers[$player['steamId']] ?? false) {
                $players[$playerIndex]['name'] = $dbPlayers[$player['steamId']]['name'];
            }
        }

        return [
            'players' => $players,
            'currentMap' => $this->currentMap,
            'roundsPlayed' => $this->roundsPlayed,
            'maps' => $maps,
        ];
    }

    public function analyseLogs(): void
    {
        // We need to get the LAST MatchStatus event for each map, then we can use these to limit the queries.
        $logs = Log::where('series_id', $this->seriesId)->where('type', 'MatchStatus')->where('data->roundsPlayed', 0)->distinct('data->map')->orderBy('id', 'desc')->get();
        $minIds = [];

        $maps = null;

        if (Cache::has('maps')) {
            $maps = Cache::get('maps');
        } else {
            $maps = Map::get();
            Cache::put('maps', $maps, Map::CACHE_TTL);
        }

        foreach ($maps as $map) {
            if ($logs->where('data.map', $map->name)->count() > 0) {
                $minIds[] = [
                    'minId' => $logs->where('data.map', $map->name)->first()?->id ?? 0,
                    'map' => $map->name,
                ];
            } else if (Log::where('series_id', $this->seriesId)->where('type', 'MatchStatus')->where('data->map', $map->name)->exists()) {
                $minIds[] = [
                    'minId' => 0,
                    'map' => $map->name
                ];
            }
        }

        $logs->groupBy('data.map')->each(function ($logs, $map) use (&$minIds) {
            $minIds[] = [
                'minId' => $logs->first()?->id ?? 1,
                'map' => $map,
            ];
        });

        foreach ($minIds as $index => $map) {
            $maxId = null;

            if (count($minIds) - 1 > $index) {
                // There's another map after this, so we use that as the maxId
                $maxId = $minIds[$index + 1]['minId'];
            }

            // Find the first MatchEnd event after this MatchStatus
            if ($matchEnd = Log::where('type', 'MatchEnd')->where('series_id', $this->seriesId)->where('id', '>', $map['minId'])->first()) {
                $maxId = $matchEnd->id;
            }

            $q = Log::where('series_id', $this->seriesId)
                ->where('id', '>=', $map['minId'])
                ->orderBy('id', 'asc');

            if ($maxId) {
                $q->where('id', '<', $maxId);
            }

            $q->chunk(5000, function (Collection $logs) {
                foreach ($logs as $log) {
                    if (method_exists($this, 'handle' . $log->type)) {
                        $this->{'handle' . $log->type}($log);
                    }
                }
            });
        }

        // $q = DB::table('logs')->where('series_id', $this->seriesId)->whereIn('type', ['MatchStatus', 'Kill', 'Attack', 'KillAssist', 'SwitchTeam'])->orderBy('id', 'asc');

        // logger('count ' . $q->count());

        // Log::where('series_id', $this->seriesId)->whereIn('type', ['MatchStatus', 'Kill', 'Attack', 'KillAssist', 'SwitchTeam'])->orderBy('id', 'asc')->chunk(5000, function (Collection $logs) {
        //     foreach ($logs as $log) {
        //         if (method_exists($this, 'handle' . $log->type)) {
        //             $this->{'handle' . $log->type}($log);
        //         }
        //     }
        // });
    }

    public function handleMatchStatus(Log $log): void
    {
        logger('MATCHSTATUS : logDataRoundsPlayed ' . $log->data['roundsPlayed'] . ' roundsPlayed ' . $this->roundsPlayed . ' currentMap ' . $this->currentMap);

        if ($log->data['roundsPlayed'] === 0) {
            if ($log->data['map']) {
                $this->maps[$log->data['map']] = [];
            }
            // reset the state
            $this->resetState();
        }

        if ($this->currentMap) {
            $this->maps[$this->currentMap]['roundsPlayed'] = $this->roundsPlayed;
            // If players is not set OR if we've reset somehow, set it.
            if ($this->maps[$this->currentMap]['players'] ?? true || $log->data['roundsPlayed'] < $this->roundsPlayed) {
                // In theory this should now only be called once per map.
                $this->maps[$this->currentMap]['players'] = $this->getMappedPlayers();
            }
        }

        $this->currentMap = $log->data['map'];
        $this->roundsPlayed = $log->data['roundsPlayed'];
    }

    public function handleKill(Log $log): void
    {
        if ($this->players[$log->data['killedSteamId']] ?? false) {
            $this->players[$log->data['killedSteamId']]['deaths']++;
        } else {
            $this->players[$log->data['killedSteamId']] = [
                'assists' => 0,
                'kills' => 0,
                'deaths' => 1,
                'damage' => 0,
                'name' => $log->data['killedName'],
                'team' => $log->data['killedTeam'],
            ];
        }

        if ($this->players[$log->data['killerSteamId']] ?? false) {
            $this->players[$log->data['killerSteamId']]['kills']++;
        } else {
            $this->players[$log->data['killerSteamId']] = [
                'assists' => 0,
                'kills' => 1,
                'deaths' => 0,
                'damage' => 0,
                'name' => $log->data['killerName'],
                'team' => $log->data['killerTeam'],
            ];
        }
    }

    public function handleAttack(Log $log): void
    {
        if ($this->players[$log->data['attackerSteamId']] ?? false) {
            $this->players[$log->data['attackerSteamId']]['damage'] += min($log->data['attackerDamage'], 100);
        } else {
            $this->players[$log->data['attackerSteamId']] = [
                'assists' => 0,
                'kills' => 0,
                'deaths' => 0,
                'damage' => min($log->data['attackerDamage'], 100),
                'name' => $log->data['attackerName'],
                'team' => $log->data['attackerTeam'],
            ];
        }
    }

    public function handleKillAssist(Log $log): void
    {
        if ($this->players[$log->data['assisterSteamId']] ?? false) {
            $this->players[$log->data['assisterSteamId']]['assists']++;
        } else {
            $this->players[$log->data['assisterSteamId']] = [
                'assists' => 1,
                'kills' => 0,
                'deaths' => 0,
                'damage' => 0,
                'name' => $log->data['assisterName'],
                'team' => $log->data['assisterTeam'],
            ];
        }
    }

    public function handleSwitchTeam(Log $log): void
    {
        if ($log->data['newTeam'] === 'Unassigned') {
            return;
        }

        if ($this->players[$log->data['steamId']] ?? false) {
            $this->players[$log->data['steamId']]['team'] = $log->data['newTeam'];
        } else {
            $this->players[$log->data['steamId']] = [
                'assists' => 0,
                'kills' => 0,
                'deaths' => 0,
                'damage' => 0,
                'team' => $log->data['newTeam'],
                'name' => $log->data['userName'],
            ];
        }
    }

    // function handleMatchEnd(Log $log): void
    // {
    //     // if ($this->currentMap) {
    //     //     if ($this->roundsPlayed > 12) {
    //     //     }
    //     // }
    // }

    // public function getTeamSide($team): ?string
    // {
    //     $results = collect($this->players)->where('team', $team)->mode('side');

    //     if (count($results)) {
    //         return $results[0];
    //     }

    //     return null;
    // }

    public function resetState(): void
    {
        $this->currentMap = null;
        $this->roundsPlayed = 0;
        $this->players = [];
    }
}
