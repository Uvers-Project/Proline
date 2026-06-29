<?php

namespace App\Http\Controllers;

use App\Models\MonthlyPlan;
use App\Models\Project;
use Illuminate\Http\Request;

class MonthlyPlanController extends Controller
{
    public function index(Request $request, Project $project)
    {
        return response()->json($project->monthlyPlans()->with('tasks')->orderBy('year')->orderBy('month')->get());
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000',
            'notes' => 'nullable|string'
        ]);

        if ($project->monthlyPlans()->where('month', $validated['month'])->where('year', $validated['year'])->exists()) {
            return response()->json(['error' => 'A plan for this month and year already exists.'], 422);
        }

        $planDate = \Carbon\Carbon::createFromDate($validated['year'], $validated['month'], 1)->startOfMonth();
        $projectStart = \Carbon\Carbon::parse($project->start_date)->startOfMonth();
        $projectEnd = \Carbon\Carbon::parse($project->end_date)->endOfMonth();

        if ($planDate->lt($projectStart) || $planDate->gt($projectEnd)) {
            return response()->json(['error' => 'The selected month falls outside the project timeline (' . $projectStart->format('M Y') . ' to ' . $projectEnd->format('M Y') . ').'], 422);
        }

        $plan = $project->monthlyPlans()->create($validated);

        // Auto-generate 4 weekly reports for this month
        $year = $validated['year'];
        $month = str_pad($validated['month'], 2, '0', STR_PAD_LEFT);
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $validated['month'], $year);

        $plan->weeklyMeetings()->create([
            'project_id' => $project->id,
            'week_number' => 1,
            'meeting_date' => "$year-$month-07",
            'date_range_start' => "$year-$month-01",
            'date_range_end' => "$year-$month-07",
        ]);
        
        $plan->weeklyMeetings()->create([
            'project_id' => $project->id,
            'week_number' => 2,
            'meeting_date' => "$year-$month-14",
            'date_range_start' => "$year-$month-08",
            'date_range_end' => "$year-$month-14",
        ]);

        $plan->weeklyMeetings()->create([
            'project_id' => $project->id,
            'week_number' => 3,
            'meeting_date' => "$year-$month-21",
            'date_range_start' => "$year-$month-15",
            'date_range_end' => "$year-$month-21",
        ]);

        $plan->weeklyMeetings()->create([
            'project_id' => $project->id,
            'week_number' => 4,
            'meeting_date' => "$year-$month-$daysInMonth",
            'date_range_start' => "$year-$month-22",
            'date_range_end' => "$year-$month-$daysInMonth",
        ]);

        return response()->json($plan->load('tasks'), 201);
    }

    public function destroy(Request $request, Project $project, MonthlyPlan $monthlyPlan)
    {
        // Ensure the plan belongs to the project
        if ($monthlyPlan->project_id !== $project->id) {
            return response()->json(['error' => 'Plan not found.'], 404);
        }

        // We can just delete it, and any cascading relationships (like tasks) should be handled by the database or model events if configured.
        // Assuming tasks are deleted via ON DELETE CASCADE or similar, or we can manually delete them:
        $monthlyPlan->tasks()->delete();
        $monthlyPlan->delete();

        return response()->json(['message' => 'Monthly plan deleted successfully.']);
    }
}
