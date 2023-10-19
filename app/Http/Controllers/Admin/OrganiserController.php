<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organisers\StoreOrganiserRequest;
use App\Http\Requests\Organisers\UpdateOrganiserRequest;
use App\Models\Organiser;
use Illuminate\Http\Request;

class OrganiserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Organiser::latest();

        if ($request->has('search')) {
            $query = $query->where('name', 'like', "%{$request->search}%");
        }

        return inertia('Admin/Organisers/Index', [
            'organisers' => $query->paginate(12),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Admin/Organisers/Form', [
            'fields' => (new Organiser())->fields(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganiserRequest $request)
    {
        $organiser = Organiser::create($request->validated());

        if ($request->has('logo') && $request->file('logo')) {
            $organiser->addMediaFromRequest('logo')
                ->toMediaCollection('logo');
        }

        return redirect()->route('admin.organisers.show', $organiser->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return inertia('Admin/Organisers/Show', [
            'organiser' => Organiser::with('users')->findOrFail($id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return inertia('Admin/Organisers/Form', [
            'organiser' => Organiser::findOrFail($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrganiserRequest $request, string $id)
    {
        $organiser = Organiser::findOrFail($id);

        $organiser->update($request->validated());

        if ($request->has('logo') && $request->file('logo')) {
            $organiser->addMediaFromRequest('logo')
                ->toMediaCollection('logo');
        }

        $organiser->save();

        return redirect()->route('admin.organisers.show', $organiser->id);
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
        return Organiser::where('name', 'like', "%{$request->search}%")
            ->limit(10)
            ->get()
            ->map(function ($organiser) {
                return [
                    'id' => $organiser->id,
                    'name' => $organiser->name,
                ];
            });
    }
}
