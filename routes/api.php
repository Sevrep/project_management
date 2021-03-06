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
use App\Http\Controllers\CardFilesController;
use App\Http\Controllers\NotesController;

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
Route::prefix('/team')->group(function () {
    Route::post('/store', [TeamsController::class, 'store']);
    Route::put('/{teams_id}', [TeamsController::class, 'update']);
    Route::delete('/{teams_id}', [TeamsController::class, 'destroy']);
});

// Team members
Route::get('/team_members', [TeamMembersController::class, 'index']);
Route::prefix('/team_member')->group(function () {
    Route::post('/store', [TeamMembersController::class, 'store']);
    Route::put('/{team_members_id}', [TeamMembersController::class, 'update']);
    Route::delete('/{team_members_id}', [TeamMembersController::class, 'destroy']);
});

// Team projects
Route::get('/team_projects', [TeamProjectsController::class, 'index']);
Route::prefix('/team_project')->group(function () {
    Route::post('/store', [TeamProjectsController::class, 'store']);
    Route::put('/{team_projects_id}', [TeamProjectsController::class, 'update']);
    Route::delete('/{team_projects_id}', [TeamProjectsController::class, 'destroy']);
});

// Projects
Route::get('/projects', [ProjectController::class, 'index']);
Route::prefix('/project')->group(function () {
    Route::post('/store', [ProjectController::class, 'store']);
    Route::put('/{project_id}', [ProjectController::class, 'update']);
    Route::delete('/{project_id}', [ProjectController::class, 'destroy']);
    Route::post('/create_test_project', [ProjectController::class, 'create_test_project']);
    Route::delete('/delete_project/{project_id}', [ProjectController::class, 'delete_project']);
});

// Boards
Route::get('/boards/{project_id}', [BoardsController::class, 'read_project_boards']);
Route::prefix('/board')->group(function () {
    Route::post('/create_board', [BoardsController::class, 'create_board']);
    Route::put('/{board_id}', [BoardsController::class, 'update_board']);
    Route::post('/create_test_board', [BoardsController::class, 'create_test_board']);
    Route::delete('/delete_board/{board_id}', [BoardsController::class, 'delete_board']);
});

// Stacks
Route::get('/stacks/{board_id}', [StacksController::class, 'read_board_stacks']);
Route::get('/read_board_done_stacks', [StacksController::class, 'read_board_done_stacks']);
Route::prefix('/stack')->group(function () {
    Route::post('/create_stack', [StacksController::class, 'create_stack']);
    Route::put('/{stack_id}', [StacksController::class, 'update_stack']);
    Route::post('/create_test_stack', [StacksController::class, 'create_test_stack']);
    Route::delete('/delete_stack/{stack_id}', [StacksController::class, 'delete_stack']);
});

// Cards
Route::get('/cards/{stack_id}/{reader}', [CardsController::class, 'read_stack_cards']);
Route::get('/read_done_cards/{reader}', [CardsController::class, 'read_done_cards']);
Route::prefix('/card')->group(function () {
    Route::post('/create_card', [CardsController::class, 'create_card']);
    Route::put('/{card_id}', [CardsController::class, 'update_card']);
    Route::put('/update_card_progress/{card_id}', [CardsController::class, 'update_card_progress']);
    Route::put('/update_card_priority/{card_id}', [CardsController::class, 'update_card_priority']);
    Route::put('/update_card_stack/{card_id}', [CardsController::class, 'update_card_stack']);
    Route::put('/update_card_stack_by/{signed_in_user}', [CardsController::class, 'update_card_stack_by']);
    Route::post('/create_test_card', [CardsController::class, 'create_test_card']);
    Route::delete('/delete_card/{card_id}', [CardsController::class, 'delete_card']);
});

// Card Files
Route::get('/read_card_files/{card_id}/{reader}', [CardFilesController::class, 'read_card_files']);
Route::post('/upload_card_file/{card_id}/{reader}', [CardFilesController::class, 'upload_card_file']);
Route::put('/update_card_file_title/{card_file_id}', [CardFilesController::class, 'update_card_file_title']);
Route::post('/create_test_card_file', [CardFilesController::class, 'create_test_card_file']);
Route::delete('/delete_card_file/{card_file_id}', [CardFilesController::class, 'delete_card_file']);

// Notes
Route::prefix('/notes')->group(function () {
    Route::get('/{card_id}/{reader}', [NotesController::class, 'read_card_notes']);
    Route::get('/read_all_ui_notes', [NotesController::class, 'read_all_ui_notes']);
    Route::get('/read_all_feedback_notes', [NotesController::class, 'read_all_feedback_notes']);
});
Route::prefix('/note')->group(function () {
    Route::post('/create_note', [NotesController::class, 'create_note']);
    Route::put('/{note_id}', [NotesController::class, 'update_note_content']);
    Route::post('/create_test_note', [NotesController::class, 'create_test_note']);
    Route::delete('/delete_note/{note_id}', [NotesController::class, 'delete_note']);
});
