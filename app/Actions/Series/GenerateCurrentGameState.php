<?php

namespace App\Actions\Series;

use App\Support\GameState\CS2GameState;
use Illuminate\Support\Collection;

class GenerateCurrentGameState
{
    public function __construct()
    {
        //
    }

    // Team A=1 Team 1 Starts CT

    public function execute(Collection $logs)
    {
        return new CS2GameState($logs);
    }
}
