<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibMunicipality extends Model
{
    protected $fillable = ['region', 'province', 'municipality'];

    public function types()
    {
        return $this->belongsToMany(LibType::class, 'lib_municipality_type_prices')
            ->withPivot('price')
            ->withTimestamps();
    }
}
