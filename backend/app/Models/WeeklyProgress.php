<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyProgress extends Model
{
    use HasFactory;

    protected $table = 'weekly_progress';

    protected $fillable = [
        'weekly_meeting_id',
        'user_id',
        'type',
        'title',
        'content',
    ];

    public function meeting()
    {
        return $this->belongsTo(WeeklyMeeting::class, 'weekly_meeting_id');
    }
}
