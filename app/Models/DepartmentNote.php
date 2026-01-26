<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentNote extends Model
{
    protected $fillable = [
        'department_id',
        'user_id',
        'title',
        'content',
        'color',
        'is_pinned',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];

    /**
     * Get the department that owns the note.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the user who created the note.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get pinned notes.
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope to filter by color.
     */
    public function scopeByColor($query, $color)
    {
        return $query->where('color', $color);
    }

    /**
     * Default ordering: pinned first, then latest.
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function ($query) {
            $query->orderBy('is_pinned', 'desc')->latest();
        });
    }
}
