<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\MonthlyPlanController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\WeeklyMeetingController;
use App\Http\Controllers\WeeklyProgressController;
use App\Http\Controllers\DailyEntryController;
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\TaskAttachmentController;

use App\Http\Controllers\WeeklyReportController;

Route::prefix('v1')->group(function () {
    // Authentication Routes
    Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        
        Route::put('/user/theme', [AuthController::class, 'updateTheme']);
        
        Route::get('/users', [AuthController::class, 'getAllUsers']);

        Route::apiResource('projects', ProjectController::class);

        Route::prefix('projects/{project}')->group(function () {
            Route::get('/timeline', [TimelineController::class, 'show']);
            Route::apiResource('members', ProjectMemberController::class)->except(['store', 'show']);
            Route::post('/invitations', [InvitationController::class, 'store']);
            Route::apiResource('monthly-plans', MonthlyPlanController::class);
            // We keep the API endpoint as monthly-tasks for backwards compatibility if needed, but rename controller
            // Actually it's better to add 'tasks' endpoint and keep 'monthly-tasks' pointing to same if needed
            Route::apiResource('tasks', TaskController::class);
            Route::post('tasks/{task}/attachments', [TaskAttachmentController::class, 'store']);
            Route::delete('tasks/{task}/attachments/{attachment}', [TaskAttachmentController::class, 'destroy']);
            
            Route::apiResource('monthly-tasks', TaskController::class);
            Route::apiResource('weekly-meetings', WeeklyMeetingController::class);
            Route::put('/weekly-progress/{weekly_progress}', [WeeklyProgressController::class, 'update']);
            Route::delete('/weekly-report', [WeeklyReportController::class, 'destroyAll']);
            Route::post('/weekly-report', [WeeklyReportController::class, 'store']);
            Route::put('/weekly-report/{meeting}', [WeeklyReportController::class, 'update']);
            Route::delete('/weekly-report/{meeting}', [WeeklyReportController::class, 'destroy']);
            Route::apiResource('daily-entries', DailyEntryController::class);
        });

        Route::post('/invitations/accept', [InvitationController::class, 'accept']);
    });
});

\Illuminate\Support\Facades\Broadcast::routes(['middleware' => ['auth:sanctum']]);
