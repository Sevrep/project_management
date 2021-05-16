<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeamProjects;

class TeamProjectsController extends Controller
{
    public function index()
    {
        return TeamProjects::get();
    }

    public function store(Request $request)
    {
        $newTeamProjects = new TeamProjects;
        $newTeamProjects->teams_id = $request->team_projects["teams_id"];
        $newTeamProjects->projects_id = $request->team_projects["projects_id"];
        $newTeamProjects->save();
        return $newTeamProjects;
    }

    public function update(Request $request, $team_projects_id)
    {
        $existingTeamProjects = TeamProjects::find($team_projects_id);

        if ($existingTeamProjects) {
            $existingTeamProjects->teams_id = $request->team_projects['teams_id'];
            $existingTeamProjects->projects_id = $request->team_projects['projects_id'];
            $existingTeamProjects->save();
            return $existingTeamProjects;
        }
        return "Team project not found.";
    }

    public function destroy($team_projects_id)
    {
        $existingTeamProjects = TeamProjects::find($team_projects_id);

        if ($existingTeamProjects) {
            $existingTeamProjects->delete();
            return "Team project " . $team_projects_id . " successfully deleted.";
        }
        return "Team project not found.";
    }
}
