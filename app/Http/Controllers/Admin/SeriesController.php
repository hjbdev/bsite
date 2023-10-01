<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Series\UpdateSeriesRequest;
use App\Models\Series;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    public function index()
    {
        return inertia('Admin/Series/Index', [
            'series' => Series::with('teamA', 'teamB')->latest()->paginate(12),
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
        return inertia('Admin/Series/Show', [
            'series' => Series::with('teamA', 'teamB', 'event')->findOrFail($id)->makeVisible('secret')
        ]);
    }
}
