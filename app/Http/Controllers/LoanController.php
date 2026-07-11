<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanCollateral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
        
        $maxLoanAmount = $isMember ? $this->getMemberAllowableAmount($user) : 0;
        $coMakers = collect();

        if (!$isMember) {
            $coMakers = User::query()
                ->where('is_aaracc', true)
                ->whereHas('capital')
                ->with('capital')
                ->orderBy('name')
                ->get()
                ->map(function (User $candidate) {
                    $allowed = $this->getMemberAllowableAmount($candidate);
                    $used = $this->getLoanExposureAmount($candidate->id);
                    $remaining = max(0, $allowed - $used);

                    $candidate->co_maker_allowed_amount = round($allowed, 2);
                    $candidate->co_maker_used_amount = round($used, 2);
                    $candidate->co_maker_remaining_amount = round($remaining, 2);

                    return $candidate;
                })
                ->filter(fn (User $candidate) => $candidate->co_maker_remaining_amount > 0)
                ->values();
        }

        return view('loans.create', compact('isMember', 'maxLoanAmount', 'coMakers'));
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
            $maxAmount = $this->getMemberAllowableAmount($user);
            $rules['loan_amount'] .= "|max:$maxAmount";
        } else {
            $rules['co_maker_id'] = [
                'required',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('is_aaracc', true)),
            ];
            $rules['collateral_type'] = 'required|string';
            $rules['collateral_description'] = 'required|string';
            $rules['estimated_value'] = 'required|numeric|min:' . ($request->loan_amount * 1.5);
            $rules['proof_of_ownership'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:5120';
        }

        $request->validate($rules, [
            'loan_amount.max' => 'As a member, you can only borrow up to 80% of your allowable capital amount.',
            'co_maker_id.required' => 'Please select an eligible co-maker.',
            'co_maker_id.exists' => 'The selected co-maker is not eligible.',
            'estimated_value.min' => 'For non-members, collateral must be valued at least 150% of the loan amount.'
        ]);

        if (!$isMember) {
            $coMaker = User::query()->with('capital')->find((int) $request->input('co_maker_id'));
            if (!$coMaker || !$coMaker->is_aaracc) {
                throw ValidationException::withMessages([
                    'co_maker_id' => 'The selected co-maker is not eligible.',
                ]);
            }

            $coMakerAllowed = $this->getMemberAllowableAmount($coMaker);
            $coMakerUsed = $this->getLoanExposureAmount($coMaker->id);
            $coMakerRemaining = max(0, $coMakerAllowed - $coMakerUsed);

            if ($coMakerRemaining <= 0) {
                throw ValidationException::withMessages([
                    'co_maker_id' => 'This co-maker already reached 80% of the allowable loan amount.',
                ]);
            }

            if ((float) $request->loan_amount > $coMakerRemaining) {
                throw ValidationException::withMessages([
                    'loan_amount' => 'For non-members, the loan amount cannot exceed the selected co-maker\'s remaining allowable amount of ₱' . number_format($coMakerRemaining, 2) . '.',
                ]);
            }
        }

        $loan = Loan::create([
            'borrower_id' => $user->id,
            'borrower_name' => $user->name,
            'borrower_type' => $isMember ? 'member' : 'non-member',
            'is_aaracc' => $isMember,
            'co_maker_id' => $isMember ? null : (int) $request->input('co_maker_id'),
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

        $loan->load(['amortizations', 'payments', 'collaterals', 'coMaker']);
        return view('loans.show', compact('loan'));
    }

    private function getMemberAllowableAmount(User $user): float
    {
        $capital = (float) ($user->capital->current_capital ?? 0);
        return round(max(0, $capital * 0.80), 2);
    }

    private function getLoanExposureAmount(int $userId): float
    {
        $statuses = ['pending', 'approved', 'active', 'overdue'];

        $borrowedAmount = (float) Loan::query()
            ->where('borrower_id', $userId)
            ->whereIn('loan_status', $statuses)
            ->sum('loan_amount');

        $coMakerAmount = (float) Loan::query()
            ->where('co_maker_id', $userId)
            ->whereIn('loan_status', $statuses)
            ->sum('loan_amount');

        return round($borrowedAmount + $coMakerAmount, 2);
    }
}
