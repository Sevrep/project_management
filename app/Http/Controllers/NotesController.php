<?php

namespace App\Http\Controllers;

use App\Models\Notes;
use App\Models\NoteNotifications;
use App\Models\NoteFileNotifications;
use App\Models\Stacks;
use App\Models\Cards;
use App\Models\CardFiles;
use App\Models\CardFileNotifications;
use App\Models\NoteFiles;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotesController extends Controller
{
    private function createNoteNotification($note_id, $reader)
    {
        $NoteNotification = new NoteNotifications;
        $NoteNotification->note_id = $note_id;
        $NoteNotification->note_notification_reader = $reader;
        $NoteNotification->save();
    }

    private function countNotificationRead($table, $column_reader, $reader, $where_id, $id)
    {
        $count = $table::where($column_reader, $reader)->where($where_id, $id)->count();
        return $count;
    }

    public function create_note(Request $request)
    {
        $card_id = $request->note["card_id"];
        $note_content = $request->note["note_content"];
        $reader = $request->note["reader"];
        $ui_requirements = isset($request->note["ui_requirements"]) ? $request->note["ui_requirements"] : 0;
        $feedback = isset($request->note["feedback"]) ? $request->note["feedback"] : 0;

        $newNote = new Notes;
        $newNote->card_id = $card_id;
        $newNote->note_content = $note_content;
        $newNote->ui_requirements = $ui_requirements;
        $newNote->feedback = $feedback;
        $newNote->save();

        if ($newNote->save()) {
            $note_id = Notes::where('card_id', $card_id)->where('note_content', $note_content)->value('note_id');
            $this->createNoteNotification($note_id, $reader);
        }

        return $newNote;
    }

    public function read_card_notes($card_id, $reader)
    {
        $finalVar = new Notes;
        $readNotificationIds = array();
        $tempArray = array();

        $card_notes = Notes::where('card_id', $card_id)->get();
        $count_card_notes = Notes::where('card_id', $card_id)->count();

        $read_note_notifications = NoteNotifications::leftJoin('notes', 'notes.note_id', '=', 'note_notifications.note_id')->leftJoin('cards', 'cards.card_id', '=', 'notes.card_id')->where('cards.card_id', $card_id)->where('note_notifications.note_notification_reader', $reader)->get();

        foreach ($read_note_notifications as $value) {
            array_push($readNotificationIds, $value->note_id);
        }

        $read_notes = 0;
        $row_number = 1;
        if ($count_card_notes > 0) {
            foreach ($card_notes as $value) {
                $note_id = $value->note_id;

                if (!in_array($note_id, $readNotificationIds)) {
                    $this->createNoteNotification($note_id, $reader);
                }

                $read_notes += NoteNotifications::where('note_notification_reader', $reader)->where('note_id', $note_id)->count();
                $note_file_notifications = NoteFileNotifications::select('note_file_notifications.note_file_id')->leftJoin('note_files', 'note_files.note_file_id', '=', 'note_file_notifications.note_file_id')->leftJoin('notes', 'notes.note_id', '=', 'note_files.note_id')->where('notes.note_id', $note_id)->get();
                $count_note_file_notifications = NoteFileNotifications::select('note_file_notifications.note_file_id')->leftJoin('note_files', 'note_files.note_file_id', '=', 'note_file_notifications.note_file_id')->leftJoin('notes', 'notes.note_id', '=', 'note_files.note_id')->where('notes.note_id', $note_id)->count();

                $tempVar = new Notes;
                $tempVar->row_number = $row_number++;
                $tempVar->note_id = $note_id;
                $tempVar->card_id = $value->card_id;
                $tempVar->note_content = $value->note_content;
                $tempVar->ui_requirements = $value->ui_requirements;
                $tempVar->feedback = $value->feedback;
                $tempVar->created_at = $value->created_at;
                $tempVar->updated_at = $value->updated_at;

                $tempVar->note_file_count = $count_note_file_notifications;
                $read_files = 0;
                foreach ($note_file_notifications as $value) {
                    $read_files += $this->countNotificationRead('NoteFileNotifications', 'note_file_notification_reader', $reader, 'note_file_id', $value->note_file_id);
                }
                $tempVar->note_file_read_count = $read_files;
                $tempVar->note_file_unread_count = $count_note_file_notifications - $read_files;

                array_push($tempArray, $tempVar);
            }

            if ($tempArray != NULL) {
                $finalVar->note_count = $count_card_notes;
                $finalVar->note_read_count = $read_notes;
                $finalVar->note_unread_count = $count_card_notes - $read_notes;
                $finalVar->data = $tempArray;
            }
        } else {
            $finalVar->error = true;
            $finalVar->message = "No record found";
        }
        return $finalVar;
    }

    public function read_all_ui_notes()
    {
        $tempArray = array();
        $finalVar = new Notes;

        $note_uis = Notes::leftJoin('cards', 'cards.card_id', '=', 'notes.card_id')->leftJoin('stacks', 'stacks.stack_id', '=', 'cards.stack_id')->leftJoin('boards', 'boards.board_id', '=', 'stacks.board_id')->where('notes.ui_requirements', '>', '0')->orderBy('notes.updated_at', 'DESC')->get();
        $count_note_uis = Notes::leftJoin('cards', 'cards.card_id', '=', 'notes.card_id')->leftJoin('stacks', 'stacks.stack_id', '=', 'cards.stack_id')->leftJoin('boards', 'boards.board_id', '=', 'stacks.board_id')->where('notes.ui_requirements', '>', '0')->orderBy('notes.updated_at', 'DESC')->count();

        if ($count_note_uis > 0) {
            foreach ($note_uis as $value) {
                $tempVar = new Notes;
                $tempVar->note_id = $value->note_id;
                $tempVar->card_id = $value->card_id;
                $tempVar->note_content = $value->note_content;
                $tempVar->ui_requirements = $value->ui_requirements;
                $tempVar->feedback = $value->feedback;
                $tempVar->created_at = $value->created_at;
                $tempVar->updated_at = $value->updated_at;
                $tempVar->stack_id = $value->stack_id;
                $tempVar->previous_stack_id = $value->previous_stack_id;
                $tempVar->card_priority = $value->card_priority;
                $tempVar->card_name = $value->card_name;
                $tempVar->card_author = $value->card_author;
                $tempVar->card_progress = $value->card_progress;
                $tempVar->completed_at = $value->completed_at;
                $tempVar->checked_by_developer = $value->checked_by_developer;
                $tempVar->checked_by_outsourcer = $value->checked_by_outsourcer;
                $tempVar->checked_by_client = $value->checked_by_client;
                $tempVar->card_created_at = $value->created_at;
                $tempVar->card_updated_at = $value->updated_at;
                $tempVar->board_id = $value->board_id;
                $tempVar->stack_name = $value->stack_name;
                $tempVar->stack_author = $value->stack_author;
                $tempVar->stack_created_at = $value->created_at;
                $tempVar->stack_updated_at = $value->updated_at;
                $tempVar->board_name = $value->board_name;
                $tempVar->board_author = $value->board_author;
                $tempVar->board_created_at = $value->created_at;
                $tempVar->board_updated_at = $value->updated_at;

                $done_stack_id_name = Stacks::select('stacks.stack_id', 'stacks.stack_name')->where('stack_name', 'DoneStacksReservedKeyword')->where('board_id', $value->board_id)->get();

                foreach ($done_stack_id_name as $value) {
                    $tempVar->done_stack_id = $value->stack_id;
                    $tempVar->done_stack_name = $value->stack_name;
                }

                array_push($tempArray, $tempVar);
            }
            $finalVar->data = $tempArray;
        } else {
            $finalVar->error = true;
            $finalVar->message = "No record found";
        }

        return $finalVar;
    }

    public function read_all_feedback_notes()
    {
        $tempArray = array();
        $finalVar = new Notes;

        $note_uis = Notes::leftJoin('cards', 'cards.card_id', '=', 'notes.card_id')->leftJoin('stacks', 'stacks.stack_id', '=', 'cards.stack_id')->leftJoin('boards', 'boards.board_id', '=', 'stacks.board_id')->where('notes.feedback', '>', '0')->orderBy('notes.updated_at', 'DESC')->get();
        $count_note_uis = Notes::leftJoin('cards', 'cards.card_id', '=', 'notes.card_id')->leftJoin('stacks', 'stacks.stack_id', '=', 'cards.stack_id')->leftJoin('boards', 'boards.board_id', '=', 'stacks.board_id')->where('notes.feedback', '>', '0')->orderBy('notes.updated_at', 'DESC')->count();

        if ($count_note_uis > 0) {
            foreach ($note_uis as $value) {
                $tempVar = new Notes;
                $tempVar->note_id = $value->note_id;
                $tempVar->card_id = $value->card_id;
                $tempVar->note_content = $value->note_content;
                $tempVar->ui_requirements = $value->ui_requirements;
                $tempVar->feedback = $value->feedback;
                $tempVar->created_at = $value->created_at;
                $tempVar->updated_at = $value->updated_at;
                $tempVar->stack_id = $value->stack_id;
                $tempVar->previous_stack_id = $value->previous_stack_id;
                $tempVar->card_priority = $value->card_priority;
                $tempVar->card_name = $value->card_name;
                $tempVar->card_author = $value->card_author;
                $tempVar->card_progress = $value->card_progress;
                $tempVar->completed_at = $value->completed_at;
                $tempVar->checked_by_developer = $value->checked_by_developer;
                $tempVar->checked_by_outsourcer = $value->checked_by_outsourcer;
                $tempVar->checked_by_client = $value->checked_by_client;
                $tempVar->card_created_at = $value->created_at;
                $tempVar->card_updated_at = $value->updated_at;
                $tempVar->board_id = $value->board_id;
                $tempVar->stack_name = $value->stack_name;
                $tempVar->stack_author = $value->stack_author;
                $tempVar->stack_created_at = $value->created_at;
                $tempVar->stack_updated_at = $value->updated_at;
                $tempVar->board_name = $value->board_name;
                $tempVar->board_author = $value->board_author;
                $tempVar->board_created_at = $value->created_at;
                $tempVar->board_updated_at = $value->updated_at;
                
                $done_stack_id_name = Stacks::select('stacks.stack_id', 'stacks.stack_name')->where('stack_name', 'DoneStacksReservedKeyword')->where('board_id', $value->board_id)->get();

                foreach ($done_stack_id_name as $value) {
                    $tempVar->done_stack_id = $value->stack_id;
                    $tempVar->done_stack_name = $value->stack_name;
                }

                array_push($tempArray, $tempVar);
            }
            $finalVar->data = $tempArray;
        } else {
            $finalVar->error = true;
            $finalVar->message = "No record found";
        }
        return $finalVar;
    }

    public function update_note_content(Request $request, $note_id)
    {
        $existingNote = Notes::find($note_id);
        if ($existingNote) {
            $existingNote->note_content = $request->note['note_content'];
            $existingNote->save();
            return $existingNote;
        }
        return "Note not found.";
    }

    public function create_test_note(Request $request)
    {
        $test = array();
        $id = $request->note["card_id"];
        $name = $request->note["note_name"];

        $newNote = new Notes;
        $newNote->card_id = $id;
        $newNote->note_content = $name;
        $newNote->ui_requirements = 1;
        $newNote->feedback = 1;
        $newNote->save();

        $newNoteNotification = new NoteNotifications;
        $newNoteNotification->note_id = $newNote->note_id;
        $newNoteNotification->note_notification_reader = $name;
        $newNoteNotification->save();

        array_push($test, $newNote);
        array_push($test, $newNoteNotification);

        return $test;
    }

    public function delete_note($note_id)
    {
        DB::statement(DB::raw("DELETE notes, note_notifications, note_files, note_file_notifications
        FROM notes
        LEFT JOIN note_notifications ON notes.note_id = note_notifications.note_id
        LEFT JOIN note_files ON notes.note_id = note_files.note_id
        LEFT JOIN note_file_notifications ON note_files.note_file_id = note_file_notifications.note_file_id
        WHERE notes.note_id = $note_id"));
    }
}
