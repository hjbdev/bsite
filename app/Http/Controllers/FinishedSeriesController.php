<?php

namespace App\Http\Controllers;

use App\Enums\SeriesStatus;
use App\Models\Series;

class FinishedSeriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Series/Index', [
            'series' => Series::with('teamA', 'teamB', 'event', 'seriesMaps')->where('status', SeriesStatus::FINISHED)->latest()->paginate(12),
            'title' => 'Results',
        ]);
    }
}
