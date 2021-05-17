<?php

namespace App\Http\Controllers;
use App\Models\Boards;

use Illuminate\Http\Request;

class BoardsController extends Controller
{

    // Create board
    public function create_board(Request $request)
    {
        $newBoard = new Boards;
        $newBoard->project_id = $request->board["project_id"];
        $newBoard->board_name = $request->board["board_name"];
        $newBoard->board_author = $request->board["board_author"];
        $newBoard->save();
        return $newBoard;
    }
    // Export board details to csv
    // Read board
    // Read all boards
    // Read all board details
    // Update board
    // Delete board

    public function create_boards() {
        $response = array();

        if($_SERVER['REQUEST_METHOD'] == 'GET') {
            
            if(isset($_GET["board_name"]) && isset($_GET["board_author"])) {
                
                $board_name = $_GET['board_name'];
                $board_author = $_GET['board_author'];

                $data = array(
                    'board_name' => $board_name,
                    'board_author' => $board_author
                );

                $query = $this->db->insert('bukkawaste_kanban_board', $data);
                
                if($query) {
                    
                    $query1 = $this->db
                        ->select('board_id')
                        ->where('board_name', $board_name)
                        ->where('board_author', $board_author)
                        ->get('bukkawaste_kanban_board');
                    
                    foreach ($query1->result() as $key => $value) {
                        $response['id'] = $value->board_id;
                    }
                    $response['message'] = "Board successfully added";
                } else {
                    $response['error'] = true;
                    $response['message'] = "Board creation failed";
                }
            } else {
                $response['error'] = true;
                $response['message'] = "Invalid parameters";
            }
        } else {
            $response['error'] = true;
            $response['message'] = "Invalid request";
        }
        echo json_encode($response);
    }
}
