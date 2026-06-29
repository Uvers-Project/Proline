<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\WeeklyProgress;

class WeeklyReportController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'week_number' => 'required|integer|min:1',
            'completed_tasks' => 'nullable|array',
            'completed_tasks.*' => 'exists:tasks,id',
            'problems' => 'nullable|array',
            'problems.*.title' => 'required|string',
            'problems.*.description' => 'required|string',
            'unexpected_tasks' => 'nullable|array',
            'unexpected_tasks.*.title' => 'required|string',
            'unexpected_tasks.*.description' => 'nullable|string',
            'unexpected_tasks.*.category' => 'nullable|string',
            'unexpected_tasks.*.priority' => 'nullable|in:Low,Medium,High',
            'unexpected_tasks.*.assigned_to' => 'nullable|exists:users,id',
            'unexpected_tasks.*.start_date' => 'nullable|date',
            'unexpected_tasks.*.end_date' => 'nullable|date',
            'unexpected_tasks.*.action' => 'required|in:new,next_week,finished',
            'next_week_tasks' => 'nullable|array',
            'next_week_tasks.*' => 'exists:tasks,id',
            'blocked_tasks' => 'nullable|array',
            'blocked_tasks.*' => 'exists:tasks,id',
        ]);

        $user = $request->user();

        // Find or create weekly meeting for the project and week
        $meeting = $project->weeklyMeetings()->firstOrCreate(
            ['week_number' => $validated['week_number']],
            [
                'meeting_date' => now(),
                'date_range_start' => now()->startOfWeek(),
                'date_range_end' => now()->endOfWeek(),
                'attendees' => [],
            ]
        );

        // 1. Process Completed Tasks
        if (!empty($validated['completed_tasks'])) {
            foreach ($validated['completed_tasks'] as $taskId) {
                $task = Task::find($taskId);
                if ($task) {
                    $task->update(['status' => 'Done']);
                    $meeting->tasks()->syncWithoutDetaching([
                        $taskId => ['relation_type' => 'completed', 'user_id' => $user->id]
                    ]);
                }
            }
        }

        // 2. Process Problems
        if (!empty($validated['problems'])) {
            foreach ($validated['problems'] as $problem) {
                WeeklyProgress::create([
                    'weekly_meeting_id' => $meeting->id,
                    'user_id' => $user->id,
                    'type' => 'problem',
                    'title' => $problem['title'],
                    'content' => $problem['description']
                ]);
            }
        }

        // 3. Process Unexpected Tasks
        if (!empty($validated['unexpected_tasks'])) {
            foreach ($validated['unexpected_tasks'] as $uTask) {
                $status = 'Backlog';
                if ($uTask['action'] === 'finished') $status = 'Done';
                elseif ($uTask['action'] === 'next_week') $status = 'In Progress';
                
                $newTask = Task::create([
                    'project_id' => $project->id,
                    'title' => $uTask['title'],
                    'description' => $uTask['description'] ?? null,
                    'status' => $status,
                    'category' => $uTask['category'] ?? 'Development',
                    'priority' => $uTask['priority'] ?? 'Medium',
                    'assigned_to' => $uTask['assigned_to'] ?? null,
                    'start_date' => $uTask['start_date'] ?? null,
                    'end_date' => $uTask['end_date'] ?? null,
                    'target_week' => $validated['week_number'],
                ]);

                if ($status === 'Done') {
                    $meeting->tasks()->syncWithoutDetaching([
                        $newTask->id => ['relation_type' => 'completed', 'user_id' => $user->id]
                    ]);
                } elseif ($status === 'In Progress') {
                    $meeting->tasks()->syncWithoutDetaching([
                        $newTask->id => ['relation_type' => 'next_todo', 'user_id' => $user->id]
                    ]);
                }
            }
        }
        
        // 4. Process Next Week Targets
        if (!empty($validated['next_week_tasks'])) {
            foreach ($validated['next_week_tasks'] as $taskId) {
                $task = Task::find($taskId);
                if ($task) {
                    $task->update([
                        'status' => 'In Progress',
                        'target_week' => $validated['week_number'] + 1,
                    ]);
                    $meeting->tasks()->syncWithoutDetaching([
                        $taskId => ['relation_type' => 'next_todo', 'user_id' => $user->id]
                    ]);
                }
            }
        }

        // 5. Process Blocked Tasks
        if (!empty($validated['blocked_tasks'])) {
            foreach ($validated['blocked_tasks'] as $taskId) {
                $task = Task::find($taskId);
                if ($task) {
                    $task->update(['status' => 'Blocked']);
                    $meeting->tasks()->syncWithoutDetaching([
                        $taskId => ['relation_type' => 'blocked', 'user_id' => $user->id]
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Weekly report submitted successfully.']);
    }

    public function update(Request $request, Project $project, $meetingId)
    {
        $meeting = $project->weeklyMeetings()->findOrFail($meetingId);

        $validated = $request->validate([
            'week_number' => 'required|integer|min:1',
            'completed_tasks' => 'nullable|array',
            'completed_tasks.*' => 'exists:tasks,id',
            'problems' => 'nullable|array',
            'problems.*.title' => 'required|string',
            'problems.*.description' => 'required|string',
            'unexpected_tasks' => 'nullable|array',
            'unexpected_tasks.*.id' => 'nullable|exists:tasks,id',
            'unexpected_tasks.*.title' => 'required|string',
            'unexpected_tasks.*.description' => 'nullable|string',
            'unexpected_tasks.*.category' => 'nullable|string',
            'unexpected_tasks.*.priority' => 'nullable|in:Low,Medium,High',
            'unexpected_tasks.*.assigned_to' => 'nullable|exists:users,id',
            'unexpected_tasks.*.start_date' => 'nullable|date',
            'unexpected_tasks.*.end_date' => 'nullable|date',
            'unexpected_tasks.*.action' => 'required|in:new,next_week,finished',
            'next_week_tasks' => 'nullable|array',
            'next_week_tasks.*' => 'exists:tasks,id',
            'blocked_tasks' => 'nullable|array',
            'blocked_tasks.*' => 'exists:tasks,id',
        ]);

        $user = $request->user();

        $meeting->update([
            'week_number' => $validated['week_number'],
        ]);

        // 1. Process Completed Tasks
        $oldCompletedTasks = $meeting->tasks()->wherePivot('user_id', $user->id)->wherePivot('relation_type', 'completed')->pluck('tasks.id')->toArray();
        $newCompletedTasks = $validated['completed_tasks'] ?? [];

        // Tasks that were unchecked (no longer reverting to In Progress)
        $uncompletedTasks = array_diff($oldCompletedTasks, $newCompletedTasks);

        $meeting->tasks()->wherePivot('user_id', $user->id)->wherePivot('relation_type', 'completed')->detach();
        if (!empty($newCompletedTasks)) {
            foreach ($newCompletedTasks as $taskId) {
                $task = Task::find($taskId);
                if ($task) {
                    $task->update(['status' => 'Done']);
                    $meeting->tasks()->attach($taskId, ['relation_type' => 'completed', 'user_id' => $user->id]);
                }
            }
        }

        // 2. Process Problems
        $meeting->progress()->where('user_id', $user->id)->where('type', 'problem')->delete();
        if (!empty($validated['problems'])) {
            foreach ($validated['problems'] as $problem) {
                WeeklyProgress::create([
                    'weekly_meeting_id' => $meeting->id,
                    'user_id' => $user->id,
                    'type' => 'problem',
                    'title' => $problem['title'],
                    'content' => $problem['description']
                ]);
            }
        }

        // 3. Process Unexpected Tasks
        if (!empty($validated['unexpected_tasks'])) {
            foreach ($validated['unexpected_tasks'] as $uTask) {
                if (!isset($uTask['id'])) {
                    $status = 'Backlog';
                    if ($uTask['action'] === 'finished') $status = 'Done';
                    elseif ($uTask['action'] === 'next_week') $status = 'In Progress';
                    
                    $newTask = Task::create([
                        'project_id' => $project->id,
                        'title' => $uTask['title'],
                        'description' => $uTask['description'] ?? null,
                        'status' => $status,
                        'category' => $uTask['category'] ?? 'Development',
                        'priority' => $uTask['priority'] ?? 'Medium',
                        'assigned_to' => $uTask['assigned_to'] ?? null,
                        'start_date' => $uTask['start_date'] ?? null,
                        'end_date' => $uTask['end_date'] ?? null,
                        'target_week' => $validated['week_number'],
                    ]);

                    if ($status === 'Done') {
                        $meeting->tasks()->attach($newTask->id, ['relation_type' => 'completed', 'user_id' => $user->id]);
                    } elseif ($status === 'In Progress') {
                        $meeting->tasks()->attach($newTask->id, ['relation_type' => 'next_todo', 'user_id' => $user->id]);
                    }
                }
            }
        }

        // 4. Process Next Week Targets
        if (!empty($validated['next_week_tasks'])) {
            foreach ($validated['next_week_tasks'] as $taskId) {
                $task = Task::find($taskId);
                if ($task) {
                    $task->update([
                        'status' => 'In Progress',
                        'target_week' => $validated['week_number'] + 1,
                    ]);
                    $meeting->tasks()->syncWithoutDetaching([
                        $taskId => ['relation_type' => 'next_todo', 'user_id' => $user->id]
                    ]);
                }
            }
        }

        // 5. Process Blocked Tasks
        $oldBlockedTasks = $meeting->tasks()->wherePivot('user_id', $user->id)->wherePivot('relation_type', 'blocked')->pluck('tasks.id')->toArray();
        $newBlockedTasks = $validated['blocked_tasks'] ?? [];

        // Tasks that were unchecked from blocked (no longer reverting to In Progress)
        $unblockedTasks = array_diff($oldBlockedTasks, $newBlockedTasks);

        $meeting->tasks()->wherePivot('user_id', $user->id)->wherePivot('relation_type', 'blocked')->detach();
        if (!empty($newBlockedTasks)) {
            foreach ($newBlockedTasks as $taskId) {
                $task = Task::find($taskId);
                if ($task) {
                    $task->update(['status' => 'Blocked']);
                    $meeting->tasks()->attach($taskId, ['relation_type' => 'blocked', 'user_id' => $user->id]);
                }
            }
        }



        return response()->json(['message' => 'Weekly report updated successfully.']);
    }

    public function destroy(Request $request, Project $project, $meetingId)
    {
        $meeting = $project->weeklyMeetings()->findOrFail($meetingId);
        
        // We no longer revert task statuses when a report is deleted.
        // The Kanban board is the single source of truth.

        $meeting->delete();
        return response()->json(['message' => 'Weekly report deleted successfully.']);
    }
    public function destroyAll(Request $request, Project $project)
    {
        // Delete all weekly meetings for this project
        $project->weeklyMeetings()->delete();
        
        // DEV PURPOSE: Reset Kanban board (move all non-Backlog tasks to In Progress)
        $project->tasks()->whereIn('status', ['Done', 'Blocked'])->update(['status' => 'In Progress']);
        
        return response()->json(['message' => 'All weekly reports deleted successfully and Kanban board reset.']);
    }
}
