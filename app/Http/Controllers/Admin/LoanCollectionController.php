<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoanCollectionController extends Controller
{
    public function index()
    {
        // Get all active and overdue loans for collection
        $loans = Loan::whereIn('loan_status', ['active', 'overdue'])
                     ->with(['amortizations' => function($q) {
                         $q->where('is_paid', false)->orderBy('due_date', 'asc');
                     }])
                     ->orderBy('id', 'desc')
                     ->get();

        return view('admin.loans.payments.index', compact('loans'));
    }
}
