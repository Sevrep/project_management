<?php

namespace App\Http\Controllers;

use App\Models\Cards;
use App\Models\CardFiles;
use App\Models\CardFileNotifications;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CardFilesController extends Controller
{
    private function createCardFileNotification($card_file_id, $reader)
    {
        $CardFileNotification = new CardFileNotifications;
        $CardFileNotification->card_file_id = $card_file_id;
        $CardFileNotification->card_file_notification_reader = $reader;
        $CardFileNotification->save();
    }

    public function upload_card_file(Request $request, $card_id, $reader)
    {
        $card_file_title = $request->card_file_title;

        $request->validate(['card_file_filename' => 'required|file|mimes:jpg,jpeg,png,mp3,m4a,wav,svg,mp4,mkv,doc,docx,xlsx,sql,rar,zip|max:209715200']);

        if ($request->file()) {

            $fileTitle = time() . '_' . $request->card_file_filename->getClientOriginalName();
            $filePath = $request->file('card_file_filename')->storeAs('uploads', $fileTitle, 'public');
            $fileStorage = "/storage/app/" . $filePath;

            if ($filePath) {

                $CardFile = new CardFiles;
                $CardFile->card_id = $card_id;
                $CardFile->card_file_title = $card_file_title;
                $CardFile->card_file_filename = $fileStorage;
                $CardFile->save();

                if ($CardFile->save()) {
                    $card_file_id = CardFiles::where('card_file_filename', $fileStorage)->value('card_file_id');
                    $this->createCardFileNotification($card_file_id, $reader);
                }
            }
        }
        return $CardFile;
    }

    // Read card_files
    public function read_card_files($card_id, $reader)
    {
        $tempArray = array();
        $readNotificationIds = array();
        $finalVar = new CardFiles;

        $card_files = CardFiles::where('card_id', $card_id)->get();
        $count_card_files = CardFiles::where('card_id', $card_id)->count();
        $read_card_file_notifications = CardFileNotifications::leftJoin('card_files', 'card_files.card_file_id', '=', 'card_file_notifications.card_file_id')->leftJoin('cards', 'cards.card_id', '=', 'card_files.card_id')->where('cards.card_id', $card_id)->where('card_file_notifications.card_file_notification_reader', '=', $reader)->get();

        foreach ($read_card_file_notifications as $value) {
            array_push($readNotificationIds, $value->card_file_id);
        }

        $readFiles = 0;
        if ($count_card_files > 0) {

            foreach ($card_files as $value) {
                $card_file_id = $value->card_file_id;
                if (!in_array($card_file_id, $readNotificationIds)) {
                    $this->createCardFileNotification($value->card_file_id, $reader);
                }
                $readFiles += CardFileNotifications::where('card_file_notification_reader', $reader)->where('card_file_id', $card_file_id)->count();
                array_push($tempArray, $value);
            }

            if ($tempArray != NULL) {
                $finalVar->card_files_count = $count_card_files;
                $finalVar->card_files_read_count = $readFiles;
                $finalVar->card_files_unread_count = $count_card_files - $readFiles;
                $finalVar->data = $tempArray;
            }
        } else {
            $finalVar->error = true;
            $finalVar->message = "No record found";
        }

        echo json_encode($finalVar);
    }
    // Update card file title
    // Update card_files
    
}
