<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class TicketStage extends Model
{


    protected $fillable = [
        'ticket_id',
        'stage_number',
        'stage_name',
        'status',
        'started_at',
        'completed_at',
        'completed_by',
        'notes'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    // Helper methods
    public function markAsInProgress()
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => $this->started_at ?? now()
        ]);
    }

    public function markAsCompleted($userId, $notes = null)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completed_by' => $userId,
            'notes' => $notes
        ]);
    }

    public function getDurationInDays()
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInDays($this->completed_at);
        }
        
        if ($this->started_at) {
            return $this->started_at->diffInDays(now());
        }
        
        return null;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
