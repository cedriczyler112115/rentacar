<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'borrower_id',
        'borrower_name',
        'borrower_type',
        'is_aaracc',
        'loan_amount',
        'interest_rate',
        'interest_type',
        'term_length_months',
        'loan_start_date',
        'due_date',
        'loan_status',
        'approved_by',
        'date_approved',
    ];

    public function borrower()
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function amortizations()
    {
        return $this->hasMany(LoanAmortization::class);
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class);
    }

    public function collaterals()
    {
        return $this->hasMany(LoanCollateral::class);
    }
}
