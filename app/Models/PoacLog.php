<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoacLog extends Model
{
    protected $fillable = [
        'poacable_id',
        'poacable_type',
        'phase',
        'title',
        'description',
        'user_id',
    ];

    public function poacable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
