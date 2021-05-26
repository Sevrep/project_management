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

        $card_notes = Notes::where('card_id', '=', $card_id)->get();
        $count_card_notes = Notes::where('card_id', '=', $card_id)->count();

        $read_note_notifications = NoteNotifications::leftJoin('notes', 'notes.note_id', '=', 'note_notifications.note_id')->leftJoin('cards', 'cards.card_id', '=', 'notes.card_id')->where('cards.card_id', $card_id)->where('note_notifications.note_notification_reader', '=', $reader)->get();

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
                $note_file_notifications = NoteFileNotifications::select('note_file_notifications.note_file_id')->leftJoin('note_files', 'note_files.note_file_id', '=', 'note_file_notifications.note_file_id')->leftJoin('notes', 'notes.note_id', '=', 'note_files.note_id')->where('notes.note_id', '=', $note_id)->get();
                $count_note_file_notifications = NoteFileNotifications::select('note_file_notifications.note_file_id')->leftJoin('note_files', 'note_files.note_file_id', '=', 'note_file_notifications.note_file_id')->leftJoin('notes', 'notes.note_id', '=', 'note_files.note_id')->where('notes.note_id', '=', $note_id)->count();

                $tempVar = new Notes;
                $tempVar->row_number = $row_number++;
                $tempVar->note_id = $note_id;
                $tempVar->card_id = $value->card_id;
                $tempVar->notes_content = $value->note_content;
                $tempVar->ui_requirements = $value->ui_requirements;
                $tempVar->feedback = $value->feedback;
                $tempVar->notes_created_at = $value->created_at;
                $tempVar->notes_updated_at = $value->updated_at;

                $tempVar->note_files_count = $count_note_file_notifications;
                $read_files = 0;
                foreach ($note_file_notifications as $value) {
                    $read_files += $this->countNotificationRead('NoteFileNotifications', 'note_file_notification_reader', $reader, 'note_file_id', $value->note_file_id);
                }
                $tempVar->note_files_read_count = $read_files;
                $tempVar->note_files_unread_count = $count_note_file_notifications - $read_files;

                array_push($tempArray, $tempVar);
            }

            if ($tempArray != NULL) {
                $finalVar->notes_count = $count_card_notes;
                $finalVar->notes_read_count = $read_notes;
                $finalVar->notes_unread_count = $count_card_notes - $read_notes;
                $finalVar->data = $tempArray;
            }
        } else {
            $finalVar->error = true;
            $finalVar->message = "No record found";
        }
        return $finalVar;
    }
    // Read ui notes
    // Read feedback notes
    // Update notes

}
