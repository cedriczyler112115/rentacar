<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'pickup_location',
        'region',
        'province',
        'municipality',
        'destination_price',
        'has_carwash',
        'carwash_fee',
        'extra_hours',
        'extra_hours_fee',
        'datetime_from',
        'datetime_to',
        'estimated_price',
        'downpayment_amount',
        'downpayment_attachments',
        'drivers_license_path',
        'additional_message',
        'referral',
        'status',
        'actual_price',
    ];

    protected $casts = [
        'has_carwash' => 'boolean',
        'downpayment_attachments' => 'array',
        'datetime_from' => 'datetime',
        'datetime_to' => 'datetime',
        'destination_price' => 'decimal:2',
        'carwash_fee' => 'decimal:2',
        'extra_hours_fee' => 'decimal:2',
        'estimated_price' => 'decimal:2',
        'downpayment_amount' => 'decimal:2',
        'actual_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function logs()
    {
        return $this->hasMany(RentalLog::class);
    }

    public function emailLogs()
    {
        return $this->hasMany(BookingEmailLog::class);
    }

    public function bookingReference(): string
    {
        return 'BK' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
