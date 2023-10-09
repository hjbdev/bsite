<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SeriesMaps\DestroySeriesMapRequest;
use App\Models\Series;

class SeriesSeriesMapController extends Controller
{
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $seriesId, string $id, DestroySeriesMapRequest $request)
    {
        $series = Series::findOrFail($seriesId);

        $series->seriesMaps()->findOrFail($id)->delete();

        return redirect()->back();
    }
}
