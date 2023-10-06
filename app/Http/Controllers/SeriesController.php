<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Series;
use App\Support\GameState\CS2GameState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SeriesController extends Controller
{
    public function index()
    {
        return inertia('Series/Index', [
            'series' => Series::with('teamA', 'teamB', 'event')->where('start_date', '>', now()->startOfDay())->orderBy('start_date')->paginate(12),
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

        $gameState = null;

        $series =  Series::with('teamA', 'teamB', 'event', 'seriesMaps.map', 'currentSeriesMap.map')->findOrFail($id);

        if (Cache::has('series-' . $series->id . '-game-state')) {
            $gameState = Cache::get('series-' . $series->id . '-game-state');
        } else {
            $gameState = (new CS2GameState(Log::where('series_id', $id)->get()))->get();
            Cache::put('series-' . $series->id . '-game-state', $gameState, CS2GameState::CACHE_TTL);
        }

        return inertia('Series/Show', [
            'series' => $series,
            'snapshot' => $gameState,
            'logs' => $series->logs()->latest()->whereIn('type', Log::BROADCASTABLE_EVENTS)->limit(10)->get(),
        ]);
    }
}
