<?php

namespace App\Policies;

use App\Models\Organiser;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrganiserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Organiser $organiser): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('store:organiser');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Organiser $organiser): bool
    {
        if ($user->can('update:organiser')) {
            return true;
        }

        if ($user->can('update:(own)organiser')) {
            return $organiser->users()?->where('id', $user->id)?->exists() ?? false;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Organiser $organiser): bool
    {
        return $user->can('delete:organiser');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Organiser $organiser): bool
    {
        return $user->can('delete:organiser');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Organiser $organiser): bool
    {
        return false;
    }
}
