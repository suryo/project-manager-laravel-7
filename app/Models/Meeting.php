<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'department_id',
        'title',
        'description',
        'meeting_date',
        'location',
        'notes',
        'created_by',
    ];

    protected $dates = [
        'meeting_date',
    ];

    public function department()
    {
        return $this->belongsTo('App\Models\Department');
    }

    public function creator()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function attendances()
    {
        return $this->hasMany('App\Models\MeetingAttendance');
    }

    public function attendees()
    {
        return $this->belongsToMany('App\Models\User', 'meeting_attendances')
            ->withPivot('status', 'notes')
            ->withTimestamps();
    }
}
