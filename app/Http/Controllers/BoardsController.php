<?php

namespace App\Http\Controllers;

use App\Models\Boards;
use App\Models\Stacks;
use App\Models\Cards;
use App\Models\CardFiles;
use App\Models\CardFileNotifications;
use App\Models\Notes;
use App\Models\NoteNotifications;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoardsController extends Controller
{
    public function create_board(Request $request)
    {
        $newBoard = new Boards;
        $newBoard->project_id = $request->board["project_id"];
        $newBoard->board_name = $request->board["board_name"];
        $newBoard->board_author = $request->board["board_author"];
        $newBoard->save();
        return $newBoard;
    }

    public function read_project_boards($project_id)
    {
        return Boards::orderBy('created_at', 'DESC')->where('project_id', $project_id)->get();
    }

    public function update_board(Request $request, $board_id)
    {
        $existingBoard = Boards::find($board_id);

        if ($existingBoard) {
            $existingBoard->board_name = $request->board['board_name'];
            $existingBoard->save();
            return $existingBoard;
        }
        return "Board not found.";
    }

    public function create_test_board(Request $request)
    {
        $test = array();
        $id = $request->board["project_id"];
        $name = $request->board["board_name"];

        $newBoard = new Boards;
        $newBoard->project_id = $id;
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

        array_push($test, $newBoard);
        array_push($test, $newStack);
        array_push($test, $newCard);
        array_push($test, $newCardFile);
        array_push($test, $newCardFileNotification);
        array_push($test, $newNote);
        array_push($test, $newNoteNotification);

        return $test;
    }

    public function delete_board($board_id)
    {
        DB::statement(DB::raw("DELETE boards, stacks, cards, card_files, card_file_notifications, notes, note_notifications, note_files, note_file_notifications
        FROM boards
        LEFT JOIN stacks ON boards.board_id = stacks.board_id
        LEFT JOIN cards ON stacks.stack_id = cards.stack_id
        LEFT JOIN card_files ON cards.card_id = card_files.card_id
        LEFT JOIN card_file_notifications ON card_files.card_file_id = card_file_notifications.card_file_id
        LEFT JOIN notes ON cards.card_id = notes.card_id
        LEFT JOIN note_notifications ON notes.note_id = note_notifications.note_id
        LEFT JOIN note_files ON notes.note_id = note_files.note_id
        LEFT JOIN note_file_notifications ON note_files.note_file_id = note_file_notifications.note_file_id
        WHERE boards.board_id = $board_id"));
    }
}
