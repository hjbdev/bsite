<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Streams\CreateSeriesStreamRequest;
use App\Models\Series;

class SeriesStreamController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(int $id)
    {
        return inertia('Admin/Streams/Form', [
            'seriesId' => $id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(int $id, CreateSeriesStreamRequest $request)
    {
        $series = Series::findOrFail($id);

        $series->streams()->create($request->validated());

        return redirect()->route('admin.series.show', $series->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $seriesId, int $id)
    {
        //
    }
}
