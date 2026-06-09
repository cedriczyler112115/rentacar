<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberCapital extends Model
{
    protected $fillable = [
        'user_id',
        'initial_capital',
        'current_capital',
        'date_invested',
        'status',
        'capital_additions_log',
    ];

    protected $casts = [
        'capital_additions_log' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
