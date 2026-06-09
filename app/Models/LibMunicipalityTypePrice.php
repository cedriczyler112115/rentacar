<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibMunicipalityTypePrice extends Model
{
    protected $table = 'lib_municipality_type_prices';

    protected $fillable = [
        'lib_municipality_id',
        'lib_type_id',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function municipality()
    {
        return $this->belongsTo(LibMunicipality::class, 'lib_municipality_id');
    }

    public function type()
    {
        return $this->belongsTo(LibType::class, 'lib_type_id');
    }
}

