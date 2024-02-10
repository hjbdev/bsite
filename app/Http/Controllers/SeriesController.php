<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Series;
use Illuminate\Support\Facades\Storage;

class SeriesController extends Controller
{
    public function index()
    {
        return inertia('Series/Index', [
            'series' => Series::with('teamA', 'teamB', 'event', 'seriesMaps')->where('start_date', '>', now()->startOfDay())->orderBy('start_date')->paginate(12),
            'title' => 'Upcoming Matches',
        ]);
    }

    public function show(string $id, ?string $slug = null)
    {
        if (! $slug) {
            $series = Series::with('teamA', 'teamB', 'event')->findOrFail($id);

            return redirect()->route('matches.show.seo', [
                'match' => $series->id,
                'slug' => $series->slug,
            ]);
        }

        $gameState = null;

        $series = Series::with('teamA.players', 'teamB.players', 'event', 'seriesMaps.map', 'currentSeriesMap.map', 'seriesMaps.players', 'streams')->findOrFail($id);

        $demos = $series->seriesMaps->map(function ($seriesMap) {
            $demo = $seriesMap->demo;
            $demoUrl = null;

            if ($demo) {
                if ($demo->disk !== 'local') {
                    $demoUrl = Storage::disk($demo->disk)->temporaryUrl($demo->path, now()->addMinutes(10));
                } else {
                    $demoUrl = Storage::disk($demo->disk)->url($demo->path);
                }
            }

            return [
                'map' => $seriesMap->map->title,
                'url' => $demoUrl,
            ];
        });

        // if (Cache::has('series-'.$series->id.'-game-state')) {
        //     $gameState = Cache::get('series-'.$series->id.'-game-state');
        // } else {
        //     $gameState = (new CS2GameState($id))->get();
        //     Cache::put('series-'.$series->id.'-game-state', $gameState, CS2GameState::CACHE_TTL);
        // }

        return inertia('Series/Show', [
            'series' => $series,
            'snapshot' => $gameState,
            'demos' => $demos,
            'logs' => $series->logs()->latest()->where('created_at', '<', now()->subSeconds($series->event->delay))->whereIn('type', Log::BROADCASTABLE_EVENTS)->limit(20)->get(),
        ]);
    }
}
