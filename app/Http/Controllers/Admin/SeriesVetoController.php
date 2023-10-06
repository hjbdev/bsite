<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SeriesVetos\DestroySeriesVetoRequest;
use App\Http\Requests\SeriesVetos\StoreSeriesVetoRequest;
use App\Models\Series;
use Illuminate\Http\Request;

class SeriesVetoController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(string $id, StoreSeriesVetoRequest $request)
    {
        $series = Series::findOrFail($id);

        $veto = $series->vetos()->create($request->validated());

        if ($veto->type === 'pick') {
            if ($series->seriesMaps()->where('map_id', $veto->map_id)->doesntExist()) {
                $series->seriesMaps()->create([
                    'map_id' => $veto->map_id,
                    'start_date' => $series->seriesMaps()->count() ? null : $series->start_date,
                ]);
            }
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $seriesId, string $id, DestroySeriesVetoRequest $request)
    {
        $series = Series::findOrFail($seriesId);

        $series->vetos()->findOrFail($id)->delete();

        return redirect()->back();
    }
}
