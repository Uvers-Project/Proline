<?php

namespace App\Http\Controllers;

use App\Models\WeeklyMeeting;
use App\Models\Project;
use Illuminate\Http\Request;

use App\Models\Task;
use Illuminate\Support\Facades\DB;

class WeeklyMeetingController extends Controller
{
    public function index(Request $request, Project $project)
    {
        return response()->json($project->weeklyMeetings()->with('tasks')->orderBy('meeting_date', 'desc')->get());
    }

    public function update(Request $request, Project $project, WeeklyMeeting $weeklyMeeting)
    {
        $validated = $request->validate([
            'completed_task_ids' => 'nullable|array',
            'completed_task_ids.*' => 'exists:monthly_tasks,id',
            'next_todo_task_ids' => 'nullable|array',
            'next_todo_task_ids.*' => 'exists:monthly_tasks,id',
            'new_tasks' => 'nullable|array',
            'new_tasks.*.title' => 'required|string',
            'new_tasks.*.description' => 'nullable|string',
            'new_tasks.*.priority' => 'in:Low,Medium,High',
            'new_tasks.*.is_completed' => 'nullable|boolean',
            'new_tasks.*.is_next_todo' => 'nullable|boolean',
            'problems' => 'nullable|array',
            'problems.*.title' => 'required|string',
            'problems.*.content' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $userId = auth()->id();
            $existingTaskIds = [];
            
            if (!empty($validated['completed_task_ids'])) {
                foreach ($validated['completed_task_ids'] as $id) {
                    $existingTaskIds[] = ['monthly_task_id' => $id, 'relation_type' => 'completed'];
                    Task::where('id', $id)->update(['status' => 'Done']);
                }
            }
            
            if (!empty($validated['next_todo_task_ids'])) {
                foreach ($validated['next_todo_task_ids'] as $id) {
                    $existingTaskIds[] = ['monthly_task_id' => $id, 'relation_type' => 'next_todo'];
                    // Update status to In Progress, unless it was just marked Done
                    if (!in_array($id, $validated['completed_task_ids'] ?? [])) {
                        Task::where('id', $id)->update(['status' => 'In Progress']);
                    }
                }
            }

            // Sync existing tasks by ONLY detaching this user's records for this meeting
            // Get all existing pivot records for this meeting that are not new_task
            $existingPivots = \DB::table('weekly_meeting_tasks')
                ->where('weekly_meeting_id', $weeklyMeeting->id)
                ->whereIn('relation_type', ['completed', 'next_todo'])
                ->get();

            \DB::table('weekly_meeting_tasks')
                ->where('weekly_meeting_id', $weeklyMeeting->id)
                ->whereIn('relation_type', ['completed', 'next_todo'])
                ->delete();

            foreach ($existingTaskIds as $pivotData) {
                // Check if this task & relation_type existed previously
                $oldRecord = $existingPivots->first(function ($item) use ($pivotData) {
                    return $item->monthly_task_id == $pivotData['monthly_task_id'] && 
                           $item->relation_type == $pivotData['relation_type'];
                });
                
                $assignedUserId = $oldRecord ? $oldRecord->user_id : $userId;

                $weeklyMeeting->tasks()->attach($pivotData['monthly_task_id'], [
                    'relation_type' => $pivotData['relation_type'],
                    'user_id' => $assignedUserId
                ]);
            }

            if (!empty($validated['new_tasks'])) {
                $maxOrder = Task::where('monthly_plan_id', $weeklyMeeting->monthly_plan_id)
                    ->where('status', 'Planned')
                    ->max('order') ?? -1;

                foreach ($validated['new_tasks'] as $newTaskData) {
                    $maxOrder++;
                    
                    $status = 'Planned';
                    if (!empty($newTaskData['is_completed'])) {
                        $status = 'Done';
                    } elseif (!empty($newTaskData['is_next_todo'])) {
                        $status = 'In Progress';
                    }

                    $newTask = Task::create([
                        'monthly_plan_id' => $weeklyMeeting->monthly_plan_id,
                        'user_id' => $userId,
                        'title' => $newTaskData['title'],
                        'description' => $newTaskData['description'] ?? null,
                        'priority' => $newTaskData['priority'] ?? 'Medium',
                        'status' => $status,
                        'order' => $maxOrder,
                    ]);
                    
                    // Always log that it was created this week
                    $weeklyMeeting->tasks()->attach($newTask->id, ['relation_type' => 'new_task', 'user_id' => $userId]);

                    // Also log if it was immediately finished or targeted for next week
                    if (!empty($newTaskData['is_completed'])) {
                        $weeklyMeeting->tasks()->attach($newTask->id, ['relation_type' => 'completed', 'user_id' => $userId]);
                    }
                    if (!empty($newTaskData['is_next_todo'])) {
                        $weeklyMeeting->tasks()->attach($newTask->id, ['relation_type' => 'next_todo', 'user_id' => $userId]);
                    }
                }
            }

            // Save Problems (only overwrite this user's problems)
            $weeklyMeeting->progress()
                ->where('type', 'problem')
                ->where('user_id', $userId)
                ->delete();

            if (!empty($validated['problems'])) {
                foreach ($validated['problems'] as $problemData) {
                    if (!empty($problemData['title'])) {
                        $weeklyMeeting->progress()->create([
                            'user_id' => $userId,
                            'type' => 'problem',
                            'title' => $problemData['title'],
                            'content' => $problemData['content'] ?? null,
                        ]);
                    }
                }
            }
            
            DB::commit();
            return response()->json($weeklyMeeting->load(['tasks', 'progress']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update weekly log', 'message' => $e->getMessage()], 500);
        }
    }
}
