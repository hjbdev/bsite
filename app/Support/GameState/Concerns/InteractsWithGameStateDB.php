<?php

namespace App\Support\GameState\Concerns;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

trait InteractsWithGameStateDB
{
    protected $stateKey;
    protected Connection $db;

    public function initialize(): void
    {
        $this->stateKey = str()->random(6);

        $databaseFile = database_path('gameStates/' . $this->stateKey . '.sqlite');

        File::ensureDirectoryExists(database_path('gameStates'));
        File::put($databaseFile, '');

        config()->set('database.connections.gameState-' . $this->stateKey, [
            'driver' => 'sqlite',
            // 'database' => $databaseFile,
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ]);

        $this->db = DB::connection('gameState-' . $this->stateKey);

        $this->db->unprepared('PRAGMA journal_mode=WAL;');

        $this->db->unprepared("CREATE TABLE players (
            steamId TEXT PRIMARY KEY,
            name TEXT,
            team TEXT,
            side TEXT,
            kills INTEGER DEFAULT 0,
            deaths INTEGER DEFAULT 0,
            assists INTEGER DEFAULT 0,
            damage INTEGER DEFAULT 0 
        )");

        $this->db->unprepared("CREATE INDEX players_steamid_index ON players (steamId)");
    }

    public function tearDown(): void
    {
        // Delete the DB
        unlink(database_path('gameStates/' . $this->stateKey . '.sqlite'));
    }

    public function players(): Builder
    {
        return $this->db->table('players');
    }
}
