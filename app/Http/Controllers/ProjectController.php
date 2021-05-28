<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Boards;
use App\Models\Stacks;
use App\Models\Cards;
use App\Models\CardFiles;
use App\Models\CardFileNotifications;
use App\Models\Notes;
use App\Models\NoteNotifications;
use App\Models\NoteFiles;
use App\Models\NoteFileNotifications;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index()
    {
        return Project::orderBy('created_at', 'DESC')->get();
    }

    public function store(Request $request)
    {
        $newProject = new Project;
        $newProject->project_name = $request->project["project_name"];
        $newProject->project_author = $request->project["project_author"];
        $newProject->save();
        return $newProject;
    }

    public function update(Request $request, $project_id)
    {
        $existingProject = Project::find($project_id);

        if ($existingProject) {
            $existingProject->project_name = $request->project['project_name'];
            $existingProject->project_author = $request->project['project_author'];
            $existingProject->save();
            return $existingProject;
        }
        return "Project not found.";
    }

    public function destroy($project_id)
    {
        $existingProject = Project::find($project_id);

        if ($existingProject) {
            $existingProject->delete();
            return "Project " . $project_id . " successfully deleted.";
        }
        return "Project not found.";
    }

    public function create_test_project(Request $request)
    {
        $test = array();
        $name = $request->project["project_name"];

        $newProject = new Project;
        $newProject->project_name = $name;
        $newProject->project_author = $name;
        $newProject->save();

        $newBoard = new Boards;
        $newBoard->project_id = $newProject->project_id;
        $newBoard->board_name = $name;
        $newBoard->board_author = $name;
        $newBoard->save();

        $newStack = new Stacks;
        $newStack->board_id = $newBoard->board_id;
        $newStack->stack_name = $name;
        $newStack->stack_author = $name;
        $newStack->save();

        $newCard = new Cards;
        $newCard->stack_id = $newStack->stack_id;
        $newCard->card_priority = "D";
        $newCard->card_name = $name;
        $newCard->card_author = $name;
        $newCard->card_progress = 55;
        $newCard->save();

        $newCardFile = new CardFiles;
        $newCardFile->card_id = $newCard->card_id;
        $newCardFile->card_file_title = $name;
        $newCardFile->card_file_filename = $name;
        $newCardFile->save();

        $newCardFileNotification = new CardFileNotifications;
        $newCardFileNotification->card_file_id = $newCardFile->card_file_id;
        $newCardFileNotification->card_file_notification_reader = $name;
        $newCardFileNotification->save();

        $newNote = new Notes;
        $newNote->card_id = $newCard->card_id;
        $newNote->note_content = $name;
        $newNote->ui_requirements = 1;
        $newNote->feedback = 1;
        $newNote->save();

        $newNoteNotification = new NoteNotifications;
        $newNoteNotification->note_id = $newNote->note_id;
        $newNoteNotification->note_notification_reader = $name;
        $newNoteNotification->save();

        array_push($test, $newProject);
        array_push($test, $newBoard);
        array_push($test, $newStack);
        array_push($test, $newCard);
        array_push($test, $newCardFile);
        array_push($test, $newCardFileNotification);
        array_push($test, $newNote);
        array_push($test, $newNoteNotification);

        return $test;
    }

    public function delete_project($project_id)
    {
        // stacks
        // DB::statement(DB::raw("DELETE stacks, cards, card_files, card_file_notifications, notes, note_notifications, note_files, note_file_notifications
        // FROM stacks        
        // LEFT JOIN cards ON stacks.stack_id = cards.stack_id
        // LEFT JOIN card_files ON cards.card_id = card_files.card_id
        // LEFT JOIN card_file_notifications ON card_files.card_file_id = card_file_notifications.card_file_id
        // LEFT JOIN notes ON cards.card_id = notes.card_id
        // LEFT JOIN note_notifications ON notes.note_id = note_notifications.note_id
        // LEFT JOIN note_files ON notes.note_id = note_files.note_id
        // LEFT JOIN note_file_notifications ON note_files.note_file_id = note_file_notifications.note_file_id
        // WHERE stacks.board_id = $project_id"));

        // projects
        $delete_project = DB::statement(DB::raw("DELETE projects, boards, stacks, cards, card_files, card_file_notifications, notes, note_notifications, note_files, note_file_notifications
        FROM projects
        LEFT JOIN boards ON projects.project_id = boards.project_id
        LEFT JOIN stacks ON boards.board_id = stacks.board_id
        LEFT JOIN cards ON stacks.stack_id = cards.stack_id
        LEFT JOIN card_files ON cards.card_id = card_files.card_id
        LEFT JOIN card_file_notifications ON card_files.card_file_id = card_file_notifications.card_file_id
        LEFT JOIN notes ON cards.card_id = notes.card_id
        LEFT JOIN note_notifications ON notes.note_id = note_notifications.note_id
        LEFT JOIN note_files ON notes.note_id = note_files.note_id
        LEFT JOIN note_file_notifications ON note_files.note_file_id = note_file_notifications.note_file_id
        WHERE projects.project_id = $project_id"));
        return $delete_project;
    }
}
