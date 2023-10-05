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

    public function show(string $id, string $slug = null)
    {
        if (!$slug) {
            $series = Series::with('teamA', 'teamB', 'event')->findOrFail($id);

            return redirect()->route('matches.show.seo', [
                'match' => $series->id,
                'slug' => $series->slug,
            ]);
        }

        $gameState = new CS2GameState(Log::where('series_id', $id)->get());
        $series =  Series::with('teamA', 'teamB', 'event', 'seriesMaps.map', 'currentSeriesMap.map')->findOrFail($id);

        return inertia('Series/Show', [
            'series' => $series,
            'snapshot' => $gameState,
            'logs' => $series->logs()->latest()->whereIn('type', ['Kill', 'RoundEnd', 'MatchStatus', 'BombPlanting'])->limit(10)->get(),
        ]);
    }
}
