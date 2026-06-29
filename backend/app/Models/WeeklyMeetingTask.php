<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WeeklyMeetingTask extends Pivot
{
    use HasFactory;

    protected $table = 'weekly_meeting_tasks';
    
    public $incrementing = true;

    protected $fillable = [
        'weekly_meeting_id',
        'monthly_task_id',
        'is_new_task',
    ];

    protected $casts = [
        'is_new_task' => 'boolean',
    ];

    public function meeting()
    {
        return $this->belongsTo(WeeklyMeeting::class, 'weekly_meeting_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'monthly_task_id');
    }
}
