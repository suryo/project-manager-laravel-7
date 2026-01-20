<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingAttendance extends Model
{
    protected $fillable = [
        'meeting_id',
        'user_id',
        'status',
        'notes',
    ];

    public function meeting()
    {
        return $this->belongsTo('App\Models\Meeting');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
