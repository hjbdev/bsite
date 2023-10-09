<?php

namespace App\Http\Controllers;

use App\Enums\SeriesStatus;
use App\Models\Series;
use Illuminate\Http\Request;

class FinishedSeriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Series/Index', [
            'series' => Series::with('teamA', 'teamB', 'event', 'seriesMaps')->where('status', SeriesStatus::FINISHED)->paginate(12),
            'title' => 'Results'
        ]);
    }
}
