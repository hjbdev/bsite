<?php

use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/teams/search', [TeamController::class, 'search'])->name('admin.teams.search');
    Route::get('/players/search', [PlayerController::class, 'search'])->name('admin.players.search');
    Route::get('/events/search', [EventController::class, 'search'])->name('admin.events.search');
});
