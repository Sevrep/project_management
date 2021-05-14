<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use App\Http\Controllers\DB;
use App\Models\Teams;

class TeamsController extends Controller
{
    public function index()
    {
        return Teams::orderBy('created_at', 'DESC')->get();
    }

    public function store(Request $request)
    {
        $newTeams = new Teams;
        $newTeams->teams_name = $request->teams["teams_name"];
        $newTeams->teams_created_by = $request->teams["teams_created_by"];
        $newTeams->save();
        return $newTeams;
    }

    public function update(Request $request, $teams_id)
    {
        $existingTeams = Teams::find($teams_id);

        if ($existingTeams) {
            $existingTeams->teams_name = $request->teams['teams_name'];
            $existingTeams->teams_created_by = $request->teams['teams_created_by'];
            $existingTeams->save();
            return $existingTeams;
        }
        return "Teams not found.";
    }

    public function destroy($teams_id)
    {
        $existingTeams = Teams::find($teams_id);

        if ($existingTeams) {
            $existingTeams->delete();
            return "Teams " . $teams_id . " successfully deleted.";
        }
        return "Teams not found.";
    }
}
