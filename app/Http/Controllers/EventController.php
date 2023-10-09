<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

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

        if (!$slug) {
            return redirect()->route('events.show.seo', [
                'match' => $event->id,
                'slug' => $event->slug,
            ]);
        }

        return inertia('Events/Show', [
            'event' => $event,
            'series' => $event->series()->orderBy('start_date', 'asc')->with('teamA', 'teamB', 'seriesMaps')->paginate(5)->setPageName('matches'),
        ]);
    }

    public function search(Request $request)
    {
        return Event::where('name', 'like', "%{$request->search}%")
            ->limit(10)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'name' => $event->name,
                ];
            });
    }
}
