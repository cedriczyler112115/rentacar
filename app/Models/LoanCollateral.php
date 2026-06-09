<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanCollateral extends Model
{
    protected $fillable = [
        'loan_id',
        'borrower_id',
        'collateral_type',
        'collateral_description',
        'estimated_value',
        'appraisal_value',
        'condition_status',
        'proof_of_ownership_path',
        'collateral_status',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function borrower()
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }
}
