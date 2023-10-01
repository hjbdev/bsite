<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Series;
use App\Support\GameState\CS2GameState;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    public function index()
    {
        return inertia('Series/Index', [
            'series' => Series::with('teamA', 'teamB', 'event')->latest()->get(),
        ]);
    }

    public function show(string $id)
    {
        $gameState = new CS2GameState(Log::where('series_id', $id)->get());

        return inertia('Series/Show', [
            'series' => Series::with('teamA', 'teamB', 'event')->findOrFail($id),
            'snapshot' => $gameState
        ]);
    }
}
