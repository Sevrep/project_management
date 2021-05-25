<?php

namespace App\Http\Controllers;

use App\Models\Notes;
use App\Models\NoteNotifications;

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
    // Read card notes
    // Read ui notes
    // Read feedback notes
    // Update notes

}
