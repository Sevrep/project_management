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
    // Read project boards
    public function read_project_boards($project_id)
    {
        return Boards::orderBy('created_at', 'DESC')->where('project_id', $project_id)->get();
    }
    // Update board
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
    // TODO
    // Delete board

    
}
