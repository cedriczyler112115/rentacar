<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibType extends Model
{
    protected $fillable = ['name', 'carwash_fee'];

    public function municipalities()
    {
        return $this->belongsToMany(LibMunicipality::class, 'lib_municipality_type_prices')
            ->withPivot('price')
            ->withTimestamps();
    }
}
