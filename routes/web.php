<?php

use App\Actions\News\GetUKCSGONews;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\OrganiserController as AdminOrganiserController;
use App\Http\Controllers\Admin\OrganiserUserController as AdminOrganiserUserController;
use App\Http\Controllers\Admin\PlayerController as AdminPlayerController;
use App\Http\Controllers\Admin\SeriesController as AdminSeriesController;
use App\Http\Controllers\Admin\SeriesSeriesMapController as AdminSeriesSeriesMapController;
use App\Http\Controllers\Admin\SeriesStreamController as AdminSeriesStreamController;
use App\Http\Controllers\Admin\SeriesVetoController as AdminSeriesVetoController;
use App\Http\Controllers\Admin\TeamController as AdminTeamController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FinishedSeriesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeriesController;
use App\Models\Event;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'recentRosterMoves' => DB::table('player_team')
            ->select('player_id', 'team_id', 'most_recent_move', 'start_date', 'end_date', 'players.name as player_name', 'players.full_name as player_full_name', 'players.nationality as player_nationality', 'teams.name as team_name')
            ->join('players', 'player_team.player_id', '=', 'players.id', 'left')
            ->join('teams', 'player_team.team_id', '=', 'teams.id', 'left')
            ->whereNotNull('most_recent_move')
            ->orderByDesc('most_recent_move')
            ->limit(8)
            ->get(),
        'upcomingEvents' => Event::where('end_date', '>=', now()->startOfDay())->orderBy('start_date')->limit(5)->get(),
        'pastEvents' => Event::where('end_date', '<', now()->startOfDay())->orderByDesc('start_date')->limit(5)->get(),
        'news' => app(GetUKCSGONews::class)->execute()->take(6),
    ]);
});

Route::inertia('/introducing-b-site', 'IntroducingBSite')->name('introducing-b-site');

Route::resource('matches', SeriesController::class)
    ->only('index', 'show')
    ->name('index', 'matches.index')
    ->name('show', 'matches.show');

Route::get('matches/{match}/{slug}', [SeriesController::class, 'show'])
    ->name('matches.show.seo');

Route::resource('results', FinishedSeriesController::class)
    ->only('index')
    ->name('index', 'results.index');

Route::resource('events', EventController::class)
    ->only('index', 'show')
    ->name('show', 'events.show');

Route::get('events/{match}/{slug}', [EventController::class, 'show'])
    ->name('events.show.seo');

/*
Route::prefix('old-admin')->middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return inertia('Dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::resource('matches', AdminSeriesController::class)
        ->name('index', 'admin.series.index')
        ->name('create', 'admin.series.create')
        ->name('store', 'admin.series.store')
        ->name('show', 'admin.series.show')
        ->name('edit', 'admin.series.edit')
        ->name('update', 'admin.series.update')
        ->name('destroy', 'admin.series.destroy');

    Route::resource('matches/{match}/streams', AdminSeriesStreamController::class)
        ->only('create', 'store', 'destroy')
        ->name('create', 'admin.series.streams.create')
        ->name('store', 'admin.series.streams.store')
        ->name('destroy', 'admin.series.streams.destroy');

    Route::resource('matches/{match}/vetos', AdminSeriesVetoController::class)
        ->only('store', 'destroy')
        ->name('store', 'admin.series.vetos.store')
        ->name('destroy', 'admin.series.vetos.destroy');

    Route::resource('matches/{match}/series-maps', AdminSeriesSeriesMapController::class)
        ->only('destroy')
        ->name('destroy', 'admin.series.series-maps.destroy');

    Route::resource('teams', AdminTeamController::class)
        ->name('index', 'admin.teams.index')
        ->name('create', 'admin.teams.create')
        ->name('store', 'admin.teams.store')
        ->name('show', 'admin.teams.show')
        ->name('edit', 'admin.teams.edit')
        ->name('update', 'admin.teams.update')
        ->name('destroy', 'admin.teams.destroy');

    Route::resource('events', AdminEventController::class)
        ->name('index', 'admin.events.index')
        ->name('create', 'admin.events.create')
        ->name('store', 'admin.events.store')
        ->name('show', 'admin.events.show')
        ->name('edit', 'admin.events.edit')
        ->name('update', 'admin.events.update')
        ->name('destroy', 'admin.events.destroy');

    Route::resource('organisers', AdminOrganiserController::class)
        ->name('index', 'admin.organisers.index')
        ->name('create', 'admin.organisers.create')
        ->name('store', 'admin.organisers.store')
        ->name('show', 'admin.organisers.show')
        ->name('edit', 'admin.organisers.edit')
        ->name('update', 'admin.organisers.update')
        ->name('destroy', 'admin.organisers.destroy');

    Route::resource('organisers/{organiser}/users', AdminOrganiserUserController::class)
        ->only('store', 'destroy')
        ->name('store', 'admin.organisers.users.store')
        ->name('destroy', 'admin.organisers.users.destroy');

    Route::resource('players', AdminPlayerController::class)
        ->name('index', 'admin.players.index')
        ->name('create', 'admin.players.create')
        ->name('store', 'admin.players.store')
        ->name('show', 'admin.players.show')
        ->name('edit', 'admin.players.edit')
        ->name('update', 'admin.players.update')
        ->name('destroy', 'admin.players.destroy');

    Route::resource('users', AdminUserController::class)
        ->only('index')
        ->name('index', 'admin.users.index');

    Route::impersonate();
});
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
