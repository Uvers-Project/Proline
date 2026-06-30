<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Project $project)
    {
        $tasks = Task::where('project_id', $project->id)->with(['subtasks', 'attachments'])->get();
        return response()->json($tasks);
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'monthly_plan_id' => 'nullable|exists:monthly_plans,id',
            'parent_id' => 'nullable|exists:tasks,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'priority' => 'in:Low,Medium,High',
            'status' => 'in:Backlog,In Progress,Blocked,Done,Cancelled',
            'blocked_description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'real_start_date' => 'nullable|date',
            'real_end_date' => 'nullable|date',
            'resolution_notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'estimated_duration' => 'nullable|numeric',
        ]);

        $validated['project_id'] = $project->id;
        
        if (!isset($validated['status'])) {
            $validated['status'] = 'Backlog';
        }

        // Get max order
        $maxOrder = Task::where('project_id', $project->id)
            ->where('status', $validated['status'])
            ->max('order');
        
        $validated['order'] = $maxOrder !== null ? $maxOrder + 1 : 0;

        $task = Task::create($validated);
        return response()->json($task, 201);
    }

    public function update(Request $request, Project $project, Task $task) // Fixed parameter name
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:Backlog,In Progress,Blocked,Done,Cancelled',
            'blocked_description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'real_start_date' => 'nullable|date',
            'real_end_date' => 'nullable|date',
            'resolution_notes' => 'nullable|string',
            'parent_id' => 'nullable|exists:tasks,id',
            'category' => 'nullable|string',
            'priority' => 'in:Low,Medium,High',
            'assigned_to' => 'nullable|exists:users,id',
            'target_week' => 'nullable|integer|min:1|max:5',
            'order' => 'sometimes|integer'
        ]);

        $task->update($validated);
        return response()->json($task->load(['subtasks', 'attachments']));
    }

    public function destroy(Project $project, Task $task)
    {
        $task->delete();
        return response()->json(null, 204);
    }
}
