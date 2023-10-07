<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
