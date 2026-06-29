<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyEntryTask extends Pivot
{
    use HasFactory;

    protected $table = 'daily_entry_tasks';
    
    public $incrementing = true;

    protected $fillable = [
        'daily_entry_id',
        'monthly_task_id',
    ];

    public function entry()
    {
        return $this->belongsTo(DailyEntry::class, 'daily_entry_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'monthly_task_id');
    }
}
