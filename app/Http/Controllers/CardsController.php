<?php

namespace App\Http\Controllers;

use App\Models\Stacks;
use App\Models\Cards;
use App\Models\CardFiles;
use App\Models\CardFileNotifications;
use App\Models\Notes;
use App\Models\NoteNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;

class CardsController extends Controller
{
    // Set card priority
    private function setCardPriority($param)
    {
        switch ($param) {
            case 'urgent':
                $priority = 'A';
                break;
            case 'high':
                $priority = 'B';
                break;
            case 'medium':
                $priority = 'C';
                break;
            default:
                $priority = 'D';
                break;
        }
        return $priority;
    }
    // TODO
    // Send mail
    private function send_mail($response_message, $id, $priority, $card_name, $stack_name, $board_name)
    {
        require base_path("vendor/autoload.php");

        $response = array();

        $sender_name = 'bukkawaste_kanban';
        $sender_email = "testernodemailertesteremail@gmail.com";

        $sender_email_username = "testernodemailertesteremail@gmail.com";
        $sender_email_password = '1Testernodemailertesteremail!';
        $recipient_email = "aljonleynes11@gmail.com";
        $subject = "Bukkawaste Project Management";
        $body = "<h1>Urgent</h1> <br>
        <h3>
        " . $card_name . " in stack" . $stack_name . " under " . $board_name . " board is marked as urgent
        </h3>
        <br><br>
        
        <h3>Bukkawaste,</h3>";

        $mail = new PHPMailer();

        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = $sender_email_username;
        $mail->Password = $sender_email_password;
        $mail->Port = 465; //587
        $mail->SMTPSecure = "ssl"; //tls

        $mail->isHTML(true);
        $mail->setFrom($sender_email, $sender_name);
        $mail->addAddress($recipient_email);
        $mail->Subject = $subject;
        $mail->Body = $body;

        // For adding attachments
        // $mail->addAttachment('uploads/kanban/IMG-1615953332.png', 'IMG-1615953332.png');

        if ($mail->send()) {
            $response['status'] = "Success";
            $response['notification'] = "Email is sent to " . $recipient_email;

            $response['id'] = $id;
            $response['priority'] = $priority;
            $response['message'] = $response_message;
        } else {
            $response['status'] = "Failed";
            $response['message'] = "Something is wrong: <br><br>" . $mail->ErrorInfo;
        }

        $response = array();

        $sender_name = 'bukkawaste_kanban';
        $sender_email = "testernodemailertesteremail@gmail.com";

        $sender_email_username = "testernodemailertesteremail@gmail.com";
        $sender_email_password = '1Testernodemailertesteremail!';
        $recipient_email = "aljonleynes11@gmail.com";
        $subject = "Bukkawaste Project Management";
        $body = "<h1>Urgent</h1> <br>
        <h3>
        " . $card_name . "card in stack" . $stack_name . " under " . $board_name . " board is marked as urgent
        </h3>
        <br><br>
        
        <h3 style='color:black'>Bukkawaste,</h3>";

        $mail = new PHPMailer();

        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = $sender_email_username;
        $mail->Password = $sender_email_password;
        $mail->Port = 465; //587
        $mail->SMTPSecure = "ssl"; //tls

        $mail->isHTML(true);
        $mail->setFrom($sender_email, $sender_name);
        $mail->addAddress($recipient_email);
        $mail->Subject = $subject;
        $mail->Body = $body;

        // For adding attachments
        // $mail->addAttachment('uploads/kanban/IMG-1615953332.png', 'IMG-1615953332.png');

        if ($mail->send()) {
            $response['status'] = "Success";
            $response['notification'] = "Email is sent to " . $recipient_email;

            $response['id'] = $id;
            $response['priority'] = $priority;
            $response['message'] = $response_message;
        } else {
            $response['status'] = "Failed";
            $response['message'] = "Something is wrong: <br><br>" . $mail->ErrorInfo;
        }
    }

    private function count_notification_read_by_user($table, $column_reader, $reader, $where_id, $id)
    {
        $count = $table::where($column_reader, $reader)->where($where_id, $id)->count();
        return $count;
    }

    // Create card
    public function create_card(Request $request)
    {
        $response = array();

        $stack_id = $request->card["stack_id"];
        $card_priority = $this->setCardPriority($request->card["card_priority"]);
        $card_name = $request->card["card_name"];
        $card_author = $request->card["card_author"];
        $card_progress = ($request->card["card_progress"]) ? $request->card["card_progress"] : 0;

        $newCard = new Cards;
        $newCard->stack_id = $stack_id;
        $newCard->card_priority = $card_priority;
        $newCard->card_name = $card_name;
        $newCard->card_author = $card_author;
        $newCard->card_progress = $card_progress;
        $newCard->save();

        if ($newCard->save()) {
            $query1 = Cards::select('card_id', 'card_priority')
                ->where('card_name', $card_name)
                ->where('card_author', $card_author)
                ->orderBy('created_at', 'DESC')
                ->get();

            $id = 0;

            foreach ($query1 as $key => $value) {
                $id = intval($value->card_id);
                $response['id'] = $value->card_id;
                $response['priority'] = $value->card_priority;
            }
            if ($card_priority == 'A') {
                $query2 = Cards::join('stacks', 'cards.stack_id', '=', 'stacks.stack_id')
                    ->join('boards', 'stacks.board_id', '=', 'boards.board_id')
                    ->where('cards.card_id', $id)
                    ->get(["cards.card_name AS card_name", "stacks.stack_name AS stack_name", "boards.board_name AS board_name"]);

                foreach ($query2 as $key => $value) {
                    $response['card_name'] = $value->card_name;
                    $response['stack_name'] = $value->stack_name;
                    $response['board_name'] = $value->board_name;
                }

                $this->send_mail(
                    "Card successfully created!",
                    $response['id'],
                    $response['priority'],
                    $response['card_name'],
                    $response['stack_name'],
                    $response['board_name']
                );
            }
        }
        return $newCard;
    }
    // Read card
    public function read_stack_cards($stack_id, $reader)
    {
        $tempArray = array();
        $finalVar = new Cards;

        $stackCards = Cards::orderBy('card_priority', 'ASC')->orderBy('updated_at', 'DESC')->where('stack_id', $stack_id)->get();

        $stackCardsCount = Cards::where('stack_id', $stack_id)->count();

        if ($stackCardsCount > 0) {

            foreach ($stackCards as $value) {

                $card_id = $value->card_id;

                $get_card_files = DB::table('card_files')->leftJoin('cards', 'cards.card_id', '=', 'card_files.card_id')->leftJoin('stacks', 'stacks.stack_id', '=', 'cards.stack_id')->select('card_files.card_file_id')->where('card_files.card_id', $card_id)->get();

                $card_files_total_count = DB::table('card_files')->leftJoin('cards', 'cards.card_id', '=', 'card_files.card_id')->leftJoin('stacks', 'stacks.stack_id', '=', 'cards.stack_id')->select('card_files.card_file_id')->where('card_files.card_id', $card_id)->count();

                $get_card_notes = DB::table('notes')->select('note_id')->where('card_id', $card_id)->get();

                $card_notes_count = DB::table('notes')->select('note_id')->where('card_id', $card_id)->count();

                $tempVar = new Cards;
                $tempVar->card_id = $value->card_id;
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
                $tempVar->created_at = $value->created_at;
                $tempVar->updated_at = $value->updated_at;

                $tempVar->card_files_total_count = $card_files_total_count;
                $readFiles = 0;
                foreach ($get_card_files as $value) {
                    $readFiles += $this->count_notification_read_by_user('CardFileNotifications', 'card_file_notification_reader', $reader, 'card_file_id', $value->card_file_id);
                }
                $tempVar->card_files_read_count = $readFiles;
                $tempVar->card_files_unread_count = $card_files_total_count - $readFiles;

                $tempVar->card_notes_count = $card_notes_count;
                $readNotes = 0;
                foreach ($get_card_notes as $value) {
                    $readNotes += $this->count_notification_read_by_user('NoteNotifications', 'note_notification_reader',  $reader, 'note_id', $value->card_file_id);
                }
                $tempVar->card_notes_read_count = $readNotes;
                $tempVar->card_notes_unread_count = $card_notes_count - $readNotes;

                array_push($tempArray, $tempVar);
            }
            $finalVar->data = ($tempArray != NULL) ? $tempArray : '';
        } else {
            $finalVar->error = true;
            $finalVar->message = "No record found";
        }

        return $finalVar;
    }
    // Read cards in done stack
    // Update card
    // Update card progress
    // Update card title
    // Update card stack
    // Update card priority

    // TODO
    // Delete card
}
