<?php

namespace App\Http\Controllers;

use App\Models\Series;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    public function index()
    {
        return inertia('Series/Index', [
            'series' => Series::with('teamA', 'teamB', 'event')->latest()->get(),
        ]);
    }

    public function show(string $id)
    {
        return inertia('Series/Show', [
            'series' => Series::with('teamA', 'teamB', 'event')->findOrFail($id),
        ]);
    }
}
