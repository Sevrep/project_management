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
        $existingProject = Project::find($project_id);
        $existingBoard = Boards::where('project_id', $existingProject->project_id)->get();
        $existingStack = Stacks::where('board_id', $existingBoard->board_id)->get();
        $existingCard = Cards::where('stack_id', $existingStack->stack_id)->get();
        $existingCardFile = CardFiles::where('card_id', $existingCard->card_id)->get();
        $existingCardFileNotification = CardFileNotifications::where('card_file_id', $existingCardFile->card_file_id)->get();
        $existingNote = Notes::where('card_id', $existingCard->card_id)->get();
        $existingNoteNotification = NoteNotifications::where('note_id', $existingNote->note_id)->get();

        if ($existingProject) {
            // $existingProject->delete();
            return "Project " . $existingProject .  $existingBoard . $existingStack . $existingCard . $existingCardFile . $existingCardFileNotification . $existingCardFileNotification . $existingNote . $existingNoteNotification ." successfully deleted.";
        }
        return "Project not found.";
    }
}
