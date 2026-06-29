<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        // Return all projects globally, as the system is a shared workspace
        $projects = Project::with('creator')->get();
        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $validated['created_by'] = $request->user()->id;

        DB::beginTransaction();
        try {
            $project = Project::create($validated);
            
            DB::commit();
            return response()->json($project, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create project.', 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Request $request, Project $project)
    {
        return response()->json($project->load(['creator', 'members']));
    }

    public function update(Request $request, Project $project)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $project->update($validated);
        
        return response()->json($project->load('creator'));
    }

    public function destroy(Request $request, Project $project)
    {

        $project->delete();
        return response()->json(['message' => 'Project deleted successfully.']);
    }
}
