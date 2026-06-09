<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalLog extends Model
{
    protected $fillable = [
        'rental_id',
        'user_id',
        'action',
        'previous_values',
        'new_values',
    ];

    protected $casts = [
        'previous_values' => 'array',
        'new_values' => 'array',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
