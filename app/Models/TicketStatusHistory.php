<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class TicketStatusHistory extends Model
{


    protected $fillable = [
        'ticket_id',
        'user_id',
        'old_status',
        'new_status',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
