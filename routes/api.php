<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/teams', [TeamsController::class, 'index']);
Route::prefix('/team')->group(function() {
    Route::post('/store', [TeamsController::class, 'store']);
    Route::put('/{teams_id}', [TeamsController::class, 'update']);
    Route::delete('/{teams_id}', [TeamsController::class, 'destroy']);
});