<?php

namespace App\Http\Controllers;
use App\Models\Stacks;

use Illuminate\Http\Request;

class StacksController extends Controller
{
    // Create stack
    public function create_stack(Request $request)
    {
        $newStack = new Stacks;
        $newStack->board_id = $request->stack["board_id"];
        $newStack->stack_name = $request->stack["stack_name"];
        $newStack->stack_author = $request->stack["stack_author"];
        $newStack->save();
        return $newStack;
    }
    // Read stack
    public function read_board_stacks($board_id)
    {
        return Stacks::orderBy('created_at', 'DESC')->where('board_id', $board_id)->get();
    }
    // Read get done stacks
    // Update stack

    // TODO
    // Delete stack
}
