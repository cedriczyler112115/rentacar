<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanAmortization extends Model
{
    protected $fillable = [
        'loan_id',
        'month_number',
        'due_date',
        'beginning_balance',
        'principal_portion',
        'interest_portion',
        'total_payment',
        'ending_balance',
        'is_paid',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
