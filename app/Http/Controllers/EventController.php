<?php

namespace App\Http\Controllers;

use App\Enums\SeriesStatus;
use App\Models\Event;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, string $slug = null)
    {
        $event = Event::findOrFail($id);

        if (! $slug) {
            return redirect()->route('events.show.seo', [
                'match' => $event->id,
                'slug' => $event->slug,
            ]);
        }

        return inertia('Events/Show', [
            'event' => $event,
            'series' => $event->series()->whereIn('status', [SeriesStatus::UPCOMING, SeriesStatus::ONGOING])->orderBy('start_date', 'asc')->with('teamA', 'teamB', 'seriesMaps')->paginate(5, pageName: 'matches'),
            'pastSeries' => $event->series()->where('status', SeriesStatus::FINISHED)->orderBy('start_date', 'desc')->with('teamA', 'teamB', 'seriesMaps')->paginate(5, pageName: 'results'),
        ]);
    }
}
