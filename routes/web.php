<?php

use App\Actions\News\GetUKCSGONews;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FinishedSeriesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeriesController;
use App\Models\Event;
use App\Models\Team;
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

    $rosterMoves = DB::table('player_team')
        ->select('player_id', 'team_id', 'most_recent_move', 'start_date', 'end_date', 'players.name as player_name', 'players.full_name as player_full_name', 'players.nationality as player_nationality', 'teams.name as team_name')
        ->join('players', 'player_team.player_id', '=', 'players.id', 'left')
        ->join('teams', 'player_team.team_id', '=', 'teams.id', 'left')
        ->whereNotNull('most_recent_move')
        ->orderByRaw('most_recent_move DESC, start_date DESC, end_date DESC')
        ->limit(8)
        ->get();

    $teams = Team::whereIn('id', $rosterMoves->pluck('team_id'))->get();

    $rosterMoves = $rosterMoves->map(function ($rosterMove) use ($teams) {
        $rosterMove->team = $teams->firstWhere('id', $rosterMove->team_id);

        return $rosterMove;
    });

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'recentRosterMoves' => $rosterMoves,
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

Route::get('/generators/upcoming-series/{series}', function (App\Models\Series $series) {
    return view('generators.upcoming-series', [
        'series' => $series,
    ]);
})->name('generators.upcoming-series');

Route::middleware('auth')->group(function () {
    Route::impersonate();
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
