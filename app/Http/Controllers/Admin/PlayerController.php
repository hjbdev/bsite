<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Players\StorePlayerRequest;
use App\Http\Requests\Players\UpdatePlayerRequest;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $query = Player::latest();

        if ($request->has('search')) {
            $query = $query->where('name', 'like', "%{$request->search}%");
        }

        return inertia('Admin/Players/Index', [
            'players' => $query->paginate(20),
        ]);
    }

    public function show(string $id, Request $request)
    {
        return inertia('Admin/Players/Show', [
            'player' => Player::findOrFail($id),
        ]);
    }

    public function create(Request $request)
    {
        return inertia('Admin/Players/Form');
    }

    public function store(StorePlayerRequest $request)
    {
        $player = Player::create([
            ...$request->validated(),
            'nationality' => $request->nationality['id'] ?? $request->nationality ?? null,
        ]);

        return redirect()->route('admin.players.show', $player);
    }

    public function edit(string $id, Request $request)
    {
        return inertia('Admin/Players/Form', [
            'player' => Player::findOrFail($id),
        ]);
    }

    public function update(string $id, UpdatePlayerRequest $request)
    {
        $player = Player::findOrFail($id);

        $player->update([
            ...$request->validated(),
            'nationality' => $request->nationality['id'] ?? $request->nationality ?? null,
        ]);

        return redirect()->route('admin.players.show', $player);
    }

    public function search(Request $request)
    {
        return Player::where('name', 'like', "%{$request->search}%")
            ->limit(10)
            ->get()
            ->map(function ($player) {
                return [
                    'id' => $player->id,
                    'name' => $player->name,
                    'steam_id64' => $player->steam_id64,
                ];
            });
    }
}
