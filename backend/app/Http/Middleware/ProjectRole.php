<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $projectId = $request->route('project');
        
        if (!$projectId) {
            return $next($request);
        }

        $membership = $request->user()->projectMemberships()
            ->where('project_id', $projectId)
            ->first();

        if (!$membership || !in_array($membership->role, $roles)) {
            return response()->json(['message' => 'Forbidden. Insufficient permissions.'], 403);
        }

        return $next($request);
    }
}
