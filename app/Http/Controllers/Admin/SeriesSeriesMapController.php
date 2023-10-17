<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SeriesMaps\DestroySeriesMapRequest;
use App\Models\Series;
use Illuminate\Support\Facades\Cache;

class SeriesSeriesMapController extends Controller
{
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $seriesId, string $id, DestroySeriesMapRequest $request)
    {
        $series = Series::findOrFail($seriesId);

        $series->seriesMaps()->findOrFail($id)->delete();

        Cache::forget('series-map-' . $id);
        Cache::forget('series-' . $series->server_token);

        return redirect()->back();
    }
}
