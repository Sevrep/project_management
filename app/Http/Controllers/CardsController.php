<?php

namespace App\Http\Controllers;

use App\Models\Cards;
use Illuminate\Http\Request;
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

        $stackCards = Cards::orderBy('card_priority', 'ASC')->orderBy('updated_at', 'DESC')->where('stack_id', $stack_id)->get();
        
        $stackCardsCount = Cards::where('stack_id', $stack_id)->count();

        if ($stackCardsCount > 0) {

            foreach ($stackCards as $value) {

                $card_id = $value->card_id;

                // $get_card_files = $this->db
                //     ->select('card_files.card_files_id')
                //     ->from('card_files')
                //     ->join('card', 'card.card_id = card_files.card_id', 'left')
                //     ->join('stack', 'stack.stack_id = card.stack_id', 'left')
                //     ->where('card_files.card_id', $card_id)
                //     ->get();

                // $get_card_notes = $this->db
                //     ->select('notes_id')
                //     ->from('notes')
                //     ->where('card_id', $card_id)
                //     ->get();

                $tempVar = new Cards;
                $tempVar->card_id = $value->card_id;
                $tempVar->stack_id = $value->stack_id;
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

                // $tempVar->card_files_total_count = $get_card_files->num_rows();
                // $readFiles = 0;
                // foreach ($get_card_files->result() as $value) {
                //     $readFiles += $this->count_card_files_notification_read_by_user($reader, $value->card_files_id);
                // }
                // $tempVar->card_files_read_count = $readFiles;
                // $tempVar->card_files_unread_count = $get_card_files->num_rows() - $readFiles;

                // $tempVar->card_notes_count = $get_card_notes->num_rows();
                // $readNotes = 0;
                // foreach ($get_card_notes->result() as $value) {
                //     $readNotes += $this->count_notes_notification_read_by_user($reader, $value->notes_id);
                // }
                // $tempVar->card_notes_read_count = $readNotes;
                // $tempVar->card_notes_unread_count = $get_card_notes->num_rows() - $readNotes;

                array_push($tempArray, $tempVar);
            }
            // if ($tempArray != NULL) {
            //     $finalVar->data = $tempArray;
            // }
        } else {
            // $finalVar->error = true;
            // $finalVar->message = "No record found";
        }

        return $tempArray;
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
