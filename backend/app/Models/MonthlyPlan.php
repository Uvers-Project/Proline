<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'month',
        'year',
        'notes',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function weeklyMeetings()
    {
        return $this->hasMany(WeeklyMeeting::class);
    }
}
