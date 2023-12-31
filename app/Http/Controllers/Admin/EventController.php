<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Events\StoreEventRequest;
use App\Http\Requests\Events\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Event::with('organiser')->latest();

        if ($request->has('search')) {
            $query = $query->where('name', 'like', "%{$request->search}%");
        }

        return inertia('Admin/Events/Index', [
            'events' => $query->paginate(20),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Admin/Events/Form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {
        $event = Event::create($request->validated());

        if ($request->has('logo') && $request->file('logo')) {
            $event->addMediaFromRequest('logo')
                ->toMediaCollection('logo');
        }

        return redirect()->route('admin.events.show', $event->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return inertia('Admin/Events/Show', [
            'event' => Event::findOrFail($id)->load('organiser'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return inertia('Admin/Events/Form', [
            'event' => Event::findOrFail($id)->load('organiser'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, string $id)
    {
        $event = Event::findOrFail($id);

        $event->update($request->validated());

        if ($request->has('logo') && $request->file('logo')) {
            $event->addMediaFromRequest('logo')
                ->toMediaCollection('logo');
        }

        $event->save();

        return redirect()->route('admin.events.show', $event->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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
