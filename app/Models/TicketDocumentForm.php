<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class TicketDocumentForm extends Model
{


    protected $fillable = [
        'ticket_document_id',
        'form_data',
    ];

    protected $casts = [
        'form_data' => 'array',
    ];

    // Relationships
    public function ticketDocument()
    {
        return $this->belongsTo(TicketDocument::class);
    }
}
