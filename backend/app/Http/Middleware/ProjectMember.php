<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectMember
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $projectParam = $request->route('project');
        
        if (!$projectParam) {
            return $next($request);
        }

        $projectId = $projectParam instanceof \App\Models\Project ? $projectParam->id : $projectParam;

        $isMember = $request->user()->projectMemberships()
            ->where('project_id', $projectId)
            ->exists();

        if (!$isMember) {
            return response()->json(['message' => 'Forbidden. You are not a member of this project.'], 403);
        }

        return $next($request);
    }
}
