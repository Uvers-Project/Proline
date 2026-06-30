<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Broadcasting\PrivateChannel;

class Task extends Model
{
    use HasFactory, SoftDeletes, BroadcastsEvents;

    public function broadcastOn($event)
    {
        return [new PrivateChannel('project.' . $this->project_id)];
    }

    protected $table = 'tasks';

    protected $fillable = [
        'project_id',
        'monthly_plan_id',
        'parent_id',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'blocked_description',
        'start_date',
        'end_date',
        'real_start_date',
        'real_end_date',
        'resolution_notes',
        'target_week',
        'estimated_duration',
        'assigned_to',
        'order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'real_start_date' => 'date',
        'real_end_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function monthlyPlan()
    {
        return $this->belongsTo(MonthlyPlan::class);
    }

    public function parentTask()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function weeklyMeetings()
    {
        return $this->belongsToMany(WeeklyMeeting::class, 'weekly_meeting_tasks', 'monthly_task_id', 'weekly_meeting_id')
                    ->withPivot('relation_type', 'user_id')
                    ->withTimestamps();
    }

    public function dailyEntries()
    {
        return $this->belongsToMany(DailyEntry::class, 'daily_entry_tasks', 'monthly_task_id', 'daily_entry_id')
                    ->withTimestamps();
    }
}
