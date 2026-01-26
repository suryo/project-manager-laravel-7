<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{


    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
    ];

    // Hierarchical relationships
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    // Get all descendants recursively
    public function allDescendants()
    {
        $descendants = collect();
        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->allDescendants());
        }
        return $descendants;
    }

    // Team members relationship
    public function members()
    {
        return $this->belongsToMany('App\Models\User', 'department_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    // Meetings relationship
    public function meetings()
    {
        return $this->hasMany('App\Models\Meeting');
    }

    // Notes relationship
    public function notes()
    {
        return $this->hasMany(DepartmentNote::class);
    }
}
