<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organisers\AddUserRequest;
use App\Http\Requests\Organisers\DestroyUserRequest;
use App\Models\Organiser;

class OrganiserUserController extends Controller
{
    public function store(string $id, AddUserRequest $request)
    {
        $organiser = Organiser::findOrFail($id);

        $organiser->users()->syncWithoutDetaching([$request->user_id]);

        return redirect()->back();
    }

    public function destroy(string $organiserId, string $id, DestroyUserRequest $request)
    {
        $organiser = Organiser::findOrFail($organiserId);

        $organiser->users()->detach($id);

        return redirect()->back();
    }
}
