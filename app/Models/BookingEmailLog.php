<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingEmailLog extends Model
{
    protected $fillable = [
        'rental_id',
        'type',
        'to_email',
        'subject',
        'status',
        'attempts',
        'error_message',
        'meta',
        'sent_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'sent_at' => 'datetime',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }
}

