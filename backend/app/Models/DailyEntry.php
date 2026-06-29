<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'date',
        'progress',
        'problems',
        'logged_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function logger()
    {
        return $this->belongsTo(User::class, 'logged_by');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'daily_entry_tasks')
                    ->withTimestamps();
    }
}
