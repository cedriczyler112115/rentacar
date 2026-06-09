<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannedRenter extends Model
{
    protected $fillable = [
        'fullname',
        'banned_details',
        'id_presented',
        'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
