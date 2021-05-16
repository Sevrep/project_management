<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeamMembers;

class TeamMembersController extends Controller
{
    public function index()
    {
        return TeamMembers::get();
    }

    public function store(Request $request)
    {
        $newTeamMembers = new TeamMembers;
        $newTeamMembers->teams_id = $request->team_members["teams_id"];
        $newTeamMembers->id = $request->team_members["id"];
        $newTeamMembers->save();
        return $newTeamMembers;
    }

    public function update(Request $request, $team_members_id)
    {
        $existingTeamMembers = TeamMembers::find($team_members_id);

        if ($existingTeamMembers) {
            $existingTeamMembers->teams_id = $request->team_members['teams_id'];
            $existingTeamMembers->id = $request->team_members['id'];
            $existingTeamMembers->save();
            return $existingTeamMembers;
        }
        return "Team member not found.";
    }

    public function destroy($team_members_id)
    {
        $existingTeamMembers = TeamMembers::find($team_members_id);

        if ($existingTeamMembers) {
            $existingTeamMembers->delete();
            return "Team member " . $team_members_id . " successfully deleted.";
        }
        return "Team member not found.";
    }
}
