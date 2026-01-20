<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentMessage extends Model
{
    protected $fillable = [
        'department_id',
        'user_id',
        'guest_name',
        'guest_email',
        'guest_contact',
        'message',
    ];

    protected $with = ['user'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
