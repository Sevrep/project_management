<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\TeamMembersController;
use App\Http\Controllers\TeamProjectsController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BoardsController;
use App\Http\Controllers\StacksController;
use App\Http\Controllers\CardsController;

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

// PRIVATE ROUTES
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// PUBLIC ROUTES
// Teams
Route::get('/teams', [TeamsController::class, 'index']);
Route::prefix('/team')->group(function() {
    Route::post('/store', [TeamsController::class, 'store']);
    Route::put('/{teams_id}', [TeamsController::class, 'update']);
    Route::delete('/{teams_id}', [TeamsController::class, 'destroy']);
});

// Team members
Route::get('/team_members', [TeamMembersController::class, 'index']);
Route::prefix('/team_member')->group(function() {
    Route::post('/store', [TeamMembersController::class, 'store']);
    Route::put('/{team_members_id}', [TeamMembersController::class, 'update']);
    Route::delete('/{team_members_id}', [TeamMembersController::class, 'destroy']);
});

// Team projects
Route::get('/team_projects', [TeamProjectsController::class, 'index']);
Route::prefix('/team_project')->group(function() {
    Route::post('/store', [TeamProjectsController::class, 'store']);
    Route::put('/{team_projects_id}', [TeamProjectsController::class, 'update']);
    Route::delete('/{team_projects_id}', [TeamProjectsController::class, 'destroy']);
});

// Projects
Route::get('/projects', [ProjectController::class, 'index']);
Route::prefix('/project')->group(function() {
    Route::post('/store', [ProjectController::class, 'store']);
    Route::put('/{project_id}', [ProjectController::class, 'update']);
    Route::delete('/{project_id}', [ProjectController::class, 'destroy']);
});

// Boards
Route::get('/boards/{project_id}', [BoardsController::class, 'read_project_boards']);
Route::prefix('/board')->group(function() {
    Route::post('/create_board', [BoardsController::class, 'create_board']);
    Route::put('/{board_id}', [BoardsController::class, 'update_board']);
    // TODO
    // Route::delete('/{project_id}', [BoardsController::class, 'destroy']);
});

// Stacks
Route::get('/stacks/{board_id}', [StacksController::class, 'read_board_stacks']);
Route::get('/read_board_done_stacks', [StacksController::class, 'read_board_done_stacks']);
Route::prefix('/stack')->group(function() {
    Route::post('/create_stack', [StacksController::class, 'create_stack']);
    Route::put('/{stack_id}', [StacksController::class, 'update_stack']);
    // TODO
    // Route::delete('/{project_id}', [StacksController::class, 'destroy']);
});

// Cards
Route::get('/cards/{stack_id}/{reader}', [CardsController::class, 'read_stack_cards']);
Route::get('/read_done_cards/{reader}', [CardsController::class, 'read_done_cards']);
Route::prefix('/card')->group(function() {
    Route::post('/create_card', [CardsController::class, 'create_card']);
    Route::put('/{card_id}', [CardsController::class, 'update_card']);
    // TODO
    // Route::delete('/{project_id}', [StacksController::class, 'destroy']);
});