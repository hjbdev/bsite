<?php

namespace App\Policies;

use App\Models\Series;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SeriesPolicy
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
    public function view(User $user, Series $series): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('store:series') || $user->can('store:(own)series');

        // if ($user->can('store:series')) {
        //     return true;
        // }

        // if ($user->can('store:(own)series')) {
        //     $event = Event::findOrFail($this->input('event.id'))->with('organiser');

        //     return $event->organiser->users()->where('id', $user->id)->exists();
        // }

        // return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Series $series): bool
    {
        if ($user->can('update:series')) {
            return true;
        }

        if ($user->can('update:(own)series')) {
            return $series->event->organiser->users()->where('id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Series $series): bool
    {
        if ($user->can('delete:series')) {
            return true;
        }

        if ($user->can('delete:(own)series')) {
            return $series->event->organiser->users()->where('id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Series $series): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Series $series): bool
    {
        return false;
    }
}
