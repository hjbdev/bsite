<?php

namespace App\Support\GameState;

use App\Models\Log;
use App\Support\GameState\Concerns\InteractsWithGameStateDB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CS2GameState
{
    use InteractsWithGameStateDB;

    public const CACHE_TTL = 60 * 5;

    public ?string $currentMap = null;
    public int $roundsPlayed = 0;
    public array $maps = [];

    // Team A = 1; Team 1 Starts CT

    public function __construct(protected int $seriesId)
    {
        $this->initialize();

        try {
            $this->analyseLogs();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->tearDown();
        }
    }

    public function get()
    {
        return [
            'players' => $this->players()->get(),
            'currentMap' => $this->currentMap,
            'roundsPlayed' => $this->roundsPlayed,
            'maps' => $this->maps,
        ];
    }

    public function analyseLogs(): void
    {
        Log::where('series_id', $this->seriesId)->orderBy('id', 'asc')->chunk(1000, function (Collection $logs) {
            foreach ($logs as $log) {
                if (method_exists($this, 'handle' . $log->type)) {
                    $this->{'handle' . $log->type}($log);
                }
            }
        });
        
    }

    public function handleMatchStatus(Log $log): void
    {

        if ($log->data['roundsPlayed'] === 0) {
            if ($log->data['map']) {
                $this->maps[$log->data['map']] = [];
            }
            // reset the state
            $this->resetState();
        }

        if ($this->currentMap) {
            $this->maps[$this->currentMap]['roundsPlayed'] = $this->roundsPlayed;
            $this->maps[$this->currentMap]['players'] = $this->players()->get();
        }

        $this->currentMap = $log->data['map'];
        $this->roundsPlayed = $log->data['roundsPlayed'];
    }

    public function handleKill(Log $log): void
    {
        $this->db->table('players')->updateOrInsert([
            'steamId' => $log->data['killedSteamId'],
        ], [
            'name' => $log->data['killedName'],
            'side' => $log->data['killedTeam'],
        ]);

        $this->players()->where('steamId', $log->data['killedSteamId'])->update([
            'deaths' => DB::raw('deaths + 1')
        ]);

        if ($this->roundsPlayed === 0) {
            $this->db->table('players')->where('steamId', $log->data['killedSteamId'])->update([
                'team' => $log->data['killedTeam'] === 'CT' ? 'a' : 'b',
            ]);
        }

        $this->db->table('players')->updateOrInsert([
            'steamId' => $log->data['killerSteamId'],
        ], [
            'name' => $log->data['killerName'],
            'side' => $log->data['killerTeam'],
        ]);

        $this->players()->where('steamId', $log->data['killerSteamId'])->update([
            'kills' => DB::raw('kills + 1')
        ]);

        if ($this->roundsPlayed === 0) {
            $this->db->table('players')->where('steamId', $log->data['killerSteamId'])->update([
                'team' => $log->data['killerTeam'] === 'CT' ? 'a' : 'b',
            ]);
        }
    }

    public function handleAttack(Log $log): void
    {
        $this->db->table('players')->updateOrInsert([
            'steamId' => $log->data['attackerSteamId'],
        ], [
            'name' => $log->data['attackerName'],
            'side' => $log->data['attackerTeam'],
        ]);

        $this->players()->where('steamId', $log->data['attackerSteamId'])->update([
            'damage' => DB::raw('damage + ' . min($log->data['attackerDamage'], 100))
        ]);
    }

    public function handleKillAssist(Log $log): void
    {
        $this->players()->updateOrInsert([
            'steamId' => $log->data['assisterSteamId'],
        ], [
            'name' => $log->data['assisterName'],
            'side' => $log->data['assisterTeam'],
        ]);

        $this->players()->where('steamId', $log->data['assisterSteamId'])->update([
            'assists' => DB::raw('assists + 1')
        ]);
    }

    public function handleSwitchTeam(Log $log): void
    {
        if ($log->data['newTeam'] === 'Unassigned') {
            return;
        }

        $this->db->table('players')->updateOrInsert([
            'steamId' => $log->data['steamId'],
        ], [
            'name' => $log->data['userName'],
            'side' => $log->data['newTeam'],
        ]);
    }

    // function handleMatchEnd(Log $log): void
    // {
    //     // if ($this->currentMap) {
    //     //     if ($this->roundsPlayed > 12) {
    //     //     }
    //     // }
    // }

    public function getTeamSide($team): ?string
    {
        $results = $this->players()->get()->where('team', $team)->mode('side');

        if (count($results)) {
            return $results[0];
        }

        return null;
    }

    public function resetState(): void
    {
        $this->currentMap = null;
        $this->roundsPlayed = 0;
        $this->db->table('players')->delete();
    }
}
