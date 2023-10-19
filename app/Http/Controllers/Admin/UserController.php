<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::latest();

        if ($request->has('search')) {
            $query = $query->where('name', 'like', '%'.$request->get('search').'%');
        }

        return inertia('Admin/Users/Index', [
            'users' => $query->paginate(20),
        ]);
    }

    public function search(Request $request)
    {
        return User::where('name', 'like', "%{$request->search}%")
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                ];
            });
    }
}
