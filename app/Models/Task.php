<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'cost',
        'start_date',
        'due_date',
        'mgmt_phase',
        'mgmt_notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_user');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }
}
