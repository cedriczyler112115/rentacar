<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CookieConsentEvent extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'preferences',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'preferences' => 'array',
    ];
}

