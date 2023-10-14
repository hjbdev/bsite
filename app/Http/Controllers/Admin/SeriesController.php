<?php

namespace App\Http\Controllers\Admin;

use App\Filters\SeriesSearchFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Series\UpdateSeriesRequest;
use App\Http\Requests\Series\StoreSeriesRequest;
use App\Models\Event;
use App\Models\Map;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SeriesController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Series::class)->with('teamA', 'teamB')
            ->orderByDesc('id')
            ->allowedFilters([
                AllowedFilter::custom('search', new SeriesSearchFilter),
                AllowedFilter::exact('event_id'),
            ]);

        $event = null;

        if ($request->has('filter.event_id')) {
            $event = Event::findOrFail($request->input('filter.event_id'))->first();
        }

        return inertia('Admin/Series/Index', [
            'series' => $query->paginate(12),
            'event' => $event,
        ]);
    }

    public function create()
    {
        return inertia('Admin/Series/Form');
    }

    public function edit(string $id)
    {
        return inertia('Admin/Series/Form', [
            'series' => Series::with('teamA', 'teamB', 'event')->findOrFail($id),
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
            'series' => Series::with('teamA', 'teamB', 'event', 'seriesMaps.map', 'vetos.map', 'vetos.team', 'streams')->findOrFail($id)->makeVisible('secret'),
            'maps' => $maps,
        ]);
    }
}
