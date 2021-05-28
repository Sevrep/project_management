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

class StacksController extends Controller
{
    public function create_stack(Request $request)
    {
        $newStack = new Stacks;
        $newStack->board_id = $request->stack["board_id"];
        $newStack->stack_name = $request->stack["stack_name"];
        $newStack->stack_author = $request->stack["stack_author"];
        $newStack->save();
        return $newStack;
    }
    
    public function read_board_stacks($board_id)
    {
        return Stacks::orderBy('created_at', 'DESC')->where('board_id', $board_id)->get();
    }
    
    public function read_board_done_stacks()
    {
        $tempArray = array();
        $done_stacks = Stacks::orderBy('created_at', 'DESC')->where('stack_name', 'DoneStacksReservedKeyword')->get();
        foreach ($done_stacks as $stack) {
            $newStack = new Stacks;
            $newStack->stack_id = $stack->stack_id;
            $newStack->board_id = $stack->board_id;
            $newStack->board_name = Boards::select('board_name')->where('board_id', $stack->board_id)->get()[0]->board_name;
            array_push($tempArray, $newStack);
        }
        return $tempArray;
    }
    
    public function update_stack(Request $request, $stack_id)
    {
        $existingStack = Stacks::find($stack_id);
        if ($existingStack) {
            $existingStack->stack_name = $request->stack['stack_name'];
            $existingStack->save();
            return $existingStack;
        }
        return "Stack not found.";
    }

    public function create_test_stack(Request $request)
    {
        $test = array();
        $id = $request->stack["board_id"];
        $name = $request->stack["stack_name"];

        $newStack = new Stacks;
        $newStack->board_id = $id;
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

        array_push($test, $newStack);
        array_push($test, $newCard);
        array_push($test, $newCardFile);
        array_push($test, $newCardFileNotification);
        array_push($test, $newNote);
        array_push($test, $newNoteNotification);

        return $test;
    }
}
