<?php

use App\Http\Controllers\Admin\SeriesController as AdminSeriesController;
use App\Http\Controllers\Admin\TeamController as AdminTeamController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeriesController;
use Illuminate\Foundation\Application;
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
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::resource('matches', SeriesController::class)
    ->name('index', 'matches.index')
    ->name('show', 'matches.show');


Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return inertia('Dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::resource('series', AdminSeriesController::class)
        ->name('index', 'admin.series.index')
        ->name('create', 'admin.series.create')
        ->name('store', 'admin.series.store')
        ->name('show', 'admin.series.show')
        ->name('edit', 'admin.series.edit')
        ->name('update', 'admin.series.update')
        ->name('destroy', 'admin.series.destroy');

    Route::resource('teams', AdminTeamController::class)
        ->name('index', 'admin.teams.index')
        ->name('create', 'admin.teams.create')
        ->name('store', 'admin.teams.store')
        ->name('show', 'admin.teams.show')
        ->name('edit', 'admin.teams.edit')
        ->name('update', 'admin.teams.update')
        ->name('destroy', 'admin.teams.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
