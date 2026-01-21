<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{

    protected $fillable = [
        'title',
        'group',
        'description',
        'project_status_id',
        'start_date',
        'end_date',
        'budget',
        'slug',
        'mgmt_phase',
        'mgmt_planning_notes',
        'mgmt_organizing_notes',
        'mgmt_actuating_notes',
        'mgmt_controlling_notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (!$project->slug) {
                $project->slug = \Illuminate\Support\Str::slug($project->title);
            }
        });

        static::updating(function ($project) {
            if ($project->isDirty('title') && !$project->isDirty('slug')) {
                $project->slug = \Illuminate\Support\Str::slug($project->title);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function status()
    {
        return $this->belongsTo(ProjectStatus::class, 'project_status_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function poacLogs()
    {
        return $this->morphMany(PoacLog::class, 'poacable')->latest();
    }
}
