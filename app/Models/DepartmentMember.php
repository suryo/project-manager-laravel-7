<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentMember extends Model
{
    protected $fillable = [
        'department_id',
        'user_id',
        'role',
        'joined_at',
    ];

    protected $dates = [
        'joined_at',
    ];

    public function department()
    {
        return $this->belongsTo('App\Models\Department');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
