<?php

namespace App\Http\Controllers;

use App\Models\Stacks;
use App\Models\Boards;

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
    // Read board done stacks
    public function read_board_done_stacks()
    {
        $tempArray = array();
        $done_stacks = Stacks::orderBy('created_at', 'DESC')->where('stack_name', 'DoneStacksReservedKeyword')->get();
        foreach ($done_stacks as $stack) {
            $newStack = new Stacks;
            $newStack->stack_id = $stack->stack_id;
            $newStack->board_id = $stack->board_id;
            $newStack->board_name = Boards::select('board_name')->where('board_id', $stack->board_id)->get()[0]->board_name;
            array_push($tempArray, $newStack);
        }
        return $tempArray;
    }
    // Update stack
    public function update_stack(Request $request, $stack_id)
    {
        $existingStack = Stacks::find($stack_id);

        if ($existingStack) {
            $existingStack->stack_name = $request->stack['stack_name'];
            $existingStack->save();
            return $existingStack;
        }
        return "Stack not found.";
    }

    // TODO
    // Delete stack
}
