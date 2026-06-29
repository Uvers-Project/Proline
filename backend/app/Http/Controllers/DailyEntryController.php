<?php

namespace App\Http\Controllers;

use App\Models\DailyEntry;
use App\Models\Project;
use Illuminate\Http\Request;

class DailyEntryController extends Controller
{
    public function index(Request $request, Project $project)
    {
        return response()->json($project->dailyEntries()->with('logger')->orderBy('date', 'desc')->get());
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'progress' => 'nullable|string',
            'problems' => 'nullable|string'
        ]);
        
        $validated['logged_by'] = $request->user()->id;

        $entry = $project->dailyEntries()->create($validated);
        return response()->json($entry->load('logger'), 201);
    }
}
