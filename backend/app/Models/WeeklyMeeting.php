<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeeklyMeeting extends Model
{
    use HasFactory, SoftDeletes;

    protected $with = ['tasks', 'progress'];

    protected $fillable = [
        'project_id',
        'monthly_plan_id',
        'week_number',
        'meeting_date',
        'date_range_start',
        'date_range_end',
        'attendees',
        'notes',
        'blocked_task_descriptions',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'date_range_start' => 'date',
        'date_range_end' => 'date',
        'attendees' => 'array',
        'blocked_task_descriptions' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function monthlyPlan()
    {
        return $this->belongsTo(MonthlyPlan::class);
    }

    public function progress()
    {
        return $this->hasMany(WeeklyProgress::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'weekly_meeting_tasks')
                    ->withPivot('relation_type', 'user_id')
                    ->withTimestamps();
    }
}
