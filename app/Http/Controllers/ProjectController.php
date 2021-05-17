<?php

namespace App\Http\Controllers;
use App\Models\Project;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        return Project::orderBy('created_at', 'DESC')->get();
    }

    public function store(Request $request)
    {
        $newProject = new Project;
        $newProject->project_name = $request->project["project_name"];
        $newProject->project_author = $request->project["project_author"];
        $newProject->save();
        return $newProject;
    }

    public function update(Request $request, $project_id)
    {
        $existingProject = Project::find($project_id);

        if ($existingProject) {
            $existingProject->project_name = $request->project['project_name'];
            $existingProject->project_author = $request->project['project_author'];
            $existingProject->save();
            return $existingProject;
        }
        return "Project not found.";
    }

    public function destroy($project_id)
    {
        $existingProject = Project::find($project_id);

        if ($existingProject) {
            $existingProject->delete();
            return "Project " . $project_id . " successfully deleted.";
        }
        return "Project not found.";
    }
}
