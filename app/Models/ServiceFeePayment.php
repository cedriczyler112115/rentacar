<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceFeePayment extends Model
{
    protected $fillable = [
        'user_id',
        'year',
        'month',
        'amount',
        'proof_path',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

