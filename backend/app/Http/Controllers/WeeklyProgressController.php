<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\WeeklyProgress;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class WeeklyProgressController extends Controller
{
    use AuthorizesRequests;

    public function update(Request $request, Project $project, WeeklyProgress $weekly_progress)
    {
        $this->authorize('view', $project);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);

        $weekly_progress->update([
            'title' => $validated['title'],
            'content' => $validated['content'] ?? '',
        ]);

        return response()->json($weekly_progress);
    }
}
