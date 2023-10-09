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
    public function show(Event $event)
    {
        return inertia('Events/Show', [
            'event' => $event->load('series.seriesMaps', 'series.teamA', 'series.teamB'),
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
