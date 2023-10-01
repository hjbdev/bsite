<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Teams\StoreTeamRequest;
use App\Http\Requests\Admin\Teams\UpdateTeamRequest;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        return inertia('Admin/Teams/Index', [
            'teams' => Team::paginate(12),
        ]);
    }

    public function show(string $id)
    {
        return inertia('Admin/Teams/Show', [
            'team' => Team::findOrFail($id)
        ]);
    }

    public function edit(string $id)
    {
        return inertia('Admin/Teams/Form', [
            'team' => Team::with('players')->findOrFail($id)
        ]);
    }

    public function update(string $id, UpdateTeamRequest $request)
    {
        $team =  Team::findOrFail($id);
        $team->update($request->validated());

        // Easier to just unlink and relink
        $team->players()->detach();

        foreach ($request->get('players') as $player) {
            if ($player['id']) {
                $team->players()->attach($player['id'], ['start_date' => $player['pivot.start_date'] ?? now()]);
                continue;
            }
        }

        return redirect()->route('admin.teams.show', $id);
    }

    public function create()
    {
        return inertia('Admin/Teams/Form');
    }

    public function store(StoreTeamRequest $request)
    {
        $team = Team::create($request->validated());

        // Easier to just unlink and relink
        $team->players()->detach();

        foreach ($request->get('players') as $player) {
            if ($player['id']) {
                $team->players()->attach($player['id'], ['start_date' => $player['pivot.start_date'] ?? now()]);
                continue;
            }
        }

        return redirect()->route('admin.teams.show', $team->id);
    }

    public function search(Request $request)
    {
        return Team::where('name', 'like', "%{$request->search}%")
            ->limit(10)
            ->get()
            ->map(function ($team) {
                return [
                    'id' => $team->id,
                    'name' => $team->name,
                ];
            });
    }
}
