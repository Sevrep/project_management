<?php

namespace App\Http\Controllers;

use App\Models\CardFiles;
use App\Models\CardFileNotifications;

use Illuminate\Http\Request;

class CardFilesController extends Controller
{
    // Upload card files
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

                    $CardFileNotification = new CardFileNotifications;
                    $CardFileNotification->card_file_id = $card_file_id;
                    $CardFileNotification->card_file_notification_reader = $reader;
                    $CardFileNotification->save();
                }
            }
        }
        return $CardFile;
    }

    // Read card_files
    // Update card file title
    // Update card_files
}
