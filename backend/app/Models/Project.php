<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
                    ->withPivot(['role', 'joined_at']);
    }

    public function projectMembers()
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function monthlyPlans()
    {
        return $this->hasMany(MonthlyPlan::class);
    }

    public function weeklyMeetings()
    {
        return $this->hasMany(WeeklyMeeting::class);
    }

    public function dailyEntries()
    {
        return $this->hasMany(DailyEntry::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
