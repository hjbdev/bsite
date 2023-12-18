<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Event;
use App\Models\Organiser;
use App\Models\Player;
use App\Models\Series;
use App\Models\User;
use App\Policies\EventPolicy;
use App\Policies\OrganiserPolicy;
use App\Policies\PlayerPolicy;
use App\Policies\RolePolicy;
use App\Policies\SeriesPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Silber\Bouncer\Database\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Event::class => EventPolicy::class,
        Organiser::class => OrganiserPolicy::class,
        Player::class => PlayerPolicy::class,
        Role::class => RolePolicy::class,
        Series::class => SeriesPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
