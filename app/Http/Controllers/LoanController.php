<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanCollateral;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index()
    {
        $loans = auth()->user()->loans()->orderBy('created_at', 'desc')->get();
        return view('loans.index', compact('loans'));
    }

    public function create()
    {
        $user = auth()->user();
        $isMember = $user->is_aaracc;
        
        $maxLoanAmount = 0;
        if ($isMember && $user->capital) {
            $maxLoanAmount = $user->capital->current_capital * 0.80; // 80% rule
        }

        return view('loans.create', compact('isMember', 'maxLoanAmount'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $isMember = $user->is_aaracc;

        $rules = [
            'loan_amount' => 'required|numeric|min:1000',
            'term_length_months' => 'required|integer|min:1|max:60',
        ];

        // Specific rules
        if ($isMember) {
            $maxAmount = $user->capital ? $user->capital->initial_capital * 0.80 : 0;
            $rules['loan_amount'] .= "|max:$maxAmount";
        } else {
            $rules['collateral_type'] = 'required|string';
            $rules['collateral_description'] = 'required|string';
            $rules['estimated_value'] = 'required|numeric|min:' . ($request->loan_amount * 1.5);
            $rules['proof_of_ownership'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:5120';
        }

        $request->validate($rules, [
            'loan_amount.max' => 'As a member, you can only borrow up to 80% of your initial capital.',
            'estimated_value.min' => 'For non-members, collateral must be valued at least 150% of the loan amount.'
        ]);

        $loan = Loan::create([
            'borrower_id' => $user->id,
            'borrower_name' => $user->name,
            'borrower_type' => $isMember ? 'member' : 'non-member',
            'is_aaracc' => $isMember,
            'loan_amount' => $request->loan_amount,
            'interest_rate' => $isMember ? 5.00 : 7.00,
            'interest_type' => 'diminishing',
            'term_length_months' => $request->term_length_months,
            'loan_status' => 'pending',
        ]);

        if (!$isMember) {
            $path = $request->file('proof_of_ownership')->store('collaterals', 'public');

            LoanCollateral::create([
                'loan_id' => $loan->id,
                'borrower_id' => $user->id,
                'collateral_type' => $request->collateral_type,
                'collateral_description' => $request->collateral_description,
                'estimated_value' => $request->estimated_value,
                'proof_of_ownership_path' => $path,
            ]);
        }

        return redirect()->route('loans.index')->with('success', 'Loan application submitted successfully and is pending approval.');
    }

    public function show(Loan $loan)
    {
        if ($loan->borrower_id !== auth()->id()) {
            abort(403);
        }

        $loan->load(['amortizations', 'payments', 'collaterals']);
        return view('loans.show', compact('loan'));
    }
}
