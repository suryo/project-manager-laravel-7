<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'avatar',
        'password',
        'monthly_energy_limit',
        'last_seen_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_seen_at' => 'datetime',
    ];

    /**
     * Get the user's avatar URL.
     *
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && \Storage::disk('public')->exists($this->avatar)) {
            return \Storage::url($this->avatar);
        }
        
        // Return null or a default placeholder if needed
        return null;
    }

    public function isOnline()
    {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(5));
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function assignedTasks()
    {
        return $this->belongsToMany(Task::class, 'task_user');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get tickets assigned to user
     */
    public function assignedTickets()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_user')
                    ->withPivot(['assigned_at', 'removed_at'])
                    ->withTimestamps();
    }

    /**
     * Calculate used energy based on active tickets
     * Formula: Sum of (Ticket Estimation * 8 / Assignee Count)
     */
    public function getUsedEnergyAttribute()
    {
        // Get active tickets (open, in_progress)
        $activeTickets = $this->assignedTickets()
            ->whereNull('ticket_user.removed_at') // Only currently assigned
            ->whereIn('status', ['open', 'in_progress', 'on_hold']) // Active statuses
            ->withCount('activeAssignees') // Load active assignee count
            ->get();
            
        $energyUsed = 0;
        
        foreach ($activeTickets as $ticket) {
            // Default estimation to 0 if not set
            $estimation = $ticket->estimation_in_days ?? 0;
            $assigneeCount = $ticket->active_assignees_count > 0 ? $ticket->active_assignees_count : 1;
            
            // Energy = (Days * 8) / Assignees
            $ticketEnergy = ($estimation * 8) / $assigneeCount;
            $energyUsed += $ticketEnergy;
        }
        
        return round($energyUsed, 1);
    }

    /**
     * Get remaining energy
     */
    public function getRemainingEnergyAttribute()
    {
        return max(0, $this->monthly_energy_limit - $this->used_energy);
    }

    /**
     * Departments where user is a member
     */
    public function departments()
    {
        return $this->belongsToMany('App\Models\Department', 'department_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Meetings user has attended
     */
    public function meetings()
    {
        return $this->belongsToMany('App\Models\Meeting', 'meeting_attendances')
            ->withPivot('status', 'notes')
            ->withTimestamps();
    }
}
