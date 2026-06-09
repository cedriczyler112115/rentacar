<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarwashServicePayment extends Model
{
    protected $fillable = [
        'user_id',
        'vehicle_id',
        'service_date',
        'amount_paid',
        'vehicle_proof_path',
    ];

    protected $casts = [
        'service_date' => 'date',
        'amount_paid' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
