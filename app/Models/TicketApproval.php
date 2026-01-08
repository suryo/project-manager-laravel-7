<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class TicketApproval extends Model
{


    protected $fillable = [
        'ticket_id',
        'document_id',
        'approver_id',
        'approver_name',
        'approver_email',
        'approval_token',
        'ip_address',
        'approval_type',
        'status',
        'comment',
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function document()
    {
        return $this->belongsTo(TicketDocument::class, 'document_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeForApprover($query, $approverId)
    {
        return $query->where('approver_id', $approverId);
    }
}
