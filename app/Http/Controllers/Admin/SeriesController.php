<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Series\UpdateSeriesRequest;
use App\Http\Requests\Series\StoreSeriesRequest;
use App\Models\Map;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SeriesController extends Controller
{
    public function index(Request $request)
    {
        $query = Series::with('teamA', 'teamB')->orderByDesc('id');

        if ($request->has('search')) {
            $query = $query->where(function ($q) use ($request) {
                $q->whereHas('teamA', function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%");
                });
                $q->orWhereHas('teamB', function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%");
                });
            });
        }

        return inertia('Admin/Series/Index', [
            'series' => $query->paginate(12),
        ]);
    }

    public function create()
    {
        return inertia('Admin/Series/Form');
    }

    public function edit(string $id)
    {
        return inertia('Admin/Series/Form', [
            'series' => Series::with('teamA', 'teamB', 'event')->findOrFail($id)
        ]);
    }

    public function store(StoreSeriesRequest $request)
    {
        $series = Series::create([
            // request->validated() without event, team_a, team_b
            ...array_diff_key($request->validated(), [
                'event' => null,
                'team_a' => null,
                'team_b' => null,
            ]),
            'type' => $request->type['id'] ?? $request->type ?? null,
            'team_a_id' => $request->team_a['id'],
            'team_b_id' => $request->team_b['id'],
            'event_id' => $request->event['id'],
        ]);

        return redirect()->route('admin.series.show', $series->id);
    }

    public function update(string $id, UpdateSeriesRequest $request)
    {
        $series = Series::findOrFail($id);

        $series->update([
            // request->validated() without event, team_a, team_b
            ...array_diff_key($request->validated(), [
                'event' => null,
                'team_a' => null,
                'team_b' => null,
            ]),
            'type' => $request->type['id'] ?? $request->type ?? null,
            'team_a_id' => $request->team_a['id'],
            'team_b_id' => $request->team_b['id'],
            'event_id' => $request->event['id'],
        ]);

        return redirect()->route('admin.series.show', $id);
    }

    public function show(string $id)
    {
        if (Cache::has('maps')) {
            $maps = Cache::get('maps');
        } else {
            $maps = Map::get();
            Cache::put('maps', $maps, Map::CACHE_TTL);
        }

        return inertia('Admin/Series/Show', [
            'series' => Series::with('teamA', 'teamB', 'event', 'seriesMaps.map')->findOrFail($id)->makeVisible('secret'),
            'maps' => $maps
        ]);
    }
}
