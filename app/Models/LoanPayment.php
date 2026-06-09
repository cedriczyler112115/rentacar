<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanPayment extends Model
{
    protected $fillable = [
        'loan_id',
        'payment_date',
        'amount_paid',
        'principal_paid',
        'interest_paid',
        'penalty',
        'remaining_balance_after_payment',
        'received_by',
        'payment_method',
        'notes',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
