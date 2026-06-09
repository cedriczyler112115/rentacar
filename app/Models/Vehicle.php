<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    /** @use HasFactory<\Database\Factories\VehicleFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'license_plate',
        'lib_brand_id',
        'price_per_day',
        'color',
        'seating_capacity',
        'lib_type_id',
        'lib_availability_status_id',
        'booked_dates',
        'lib_transmission_id',
        'lib_fuel_type_id',
        'displacement',
        'year_model',
        'user_id',
    ];

    protected $casts = [
        'booked_dates' => 'array',
    ];

    public function images()
    {
        return $this->hasMany(VehicleImage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function libType()
    {
        return $this->belongsTo(LibType::class, 'lib_type_id');
    }

    public function libAvailabilityStatus()
    {
        return $this->belongsTo(LibAvailabilityStatus::class, 'lib_availability_status_id');
    }

    public function libTransmission()
    {
        return $this->belongsTo(LibTransmission::class, 'lib_transmission_id');
    }

    public function libBrand()
    {
        return $this->belongsTo(LibBrand::class, 'lib_brand_id');
    }

    public function libFuelType()
    {
        return $this->belongsTo(LibFuelType::class, 'lib_fuel_type_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
