<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanAmortization;
use App\Models\LoanPayment;
use App\Models\MemberCapital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoanController extends Controller
{
    public function index()
    {
        $today = now()->startOfDay();

        $overdueLoanIds = LoanAmortization::query()
            ->where('is_paid', false)
            ->whereDate('due_date', '<', $today->toDateString())
            ->distinct()
            ->pluck('loan_id');

        Loan::query()
            ->whereIn('id', $overdueLoanIds)
            ->where('loan_status', 'active')
            ->update(['loan_status' => 'overdue']);

        Loan::query()
            ->where('loan_status', 'overdue')
            ->whereNotIn('id', $overdueLoanIds)
            ->update(['loan_status' => 'active']);

        $loans = Loan::with('borrower')->orderBy('created_at', 'desc')->get();
        
        // Analytics
        $totalActiveLoans = Loan::where('loan_status', 'active')->count();
        $totalReleasedAmount = Loan::whereIn('loan_status', ['active', 'completed', 'overdue'])->sum('loan_amount');
        $totalCollectedPayments = LoanPayment::sum('amount_paid');
        $totalCollectedPenalties = LoanPayment::sum('penalty');
        $overdueLoans = Loan::where('loan_status', 'overdue')->count();
        $totalMemberCapitalAccumulated = (float) MemberCapital::sum('current_capital');
        
        return view('admin.loans.index', compact('loans', 'totalActiveLoans', 'totalReleasedAmount', 'totalCollectedPayments', 'totalCollectedPenalties', 'overdueLoans', 'totalMemberCapitalAccumulated'));
    }

    public function show(Loan $loan)
    {
        $loan->load(['borrower', 'amortizations', 'payments', 'collaterals', 'approver', 'coMaker']);
        return view('admin.loans.show', compact('loan'));
    }

    public function approve(Request $request, Loan $loan)
    {
        if ($loan->loan_status !== 'pending') {
            return back()->with('error', 'Only pending loans can be approved.');
        }

        $loan->update([
            'loan_status' => 'active',
            'approved_by' => auth()->id(),
            'date_approved' => now(),
            'loan_start_date' => now(),
            'due_date' => now()->addMonths($loan->term_length_months)
        ]);

        // Generate Amortization Schedule (Equal Principal Diminishing Interest)
        $principal = $loan->loan_amount;
        $monthlyInterestRate = $loan->interest_rate / 100; // Treated as monthly rate as per formula
        $months = $loan->term_length_months;

        $fixedMonthlyPrincipal = $principal / $months;

        $balance = $principal;
        $startDate = now();
        $accumulatedPrincipal = 0;

        for ($i = 1; $i <= $months; $i++) {
            $interestPortion = ceil(round($balance * $monthlyInterestRate * 100, 4)) / 100;
            $principalPortion = ceil(round($fixedMonthlyPrincipal * 100, 4)) / 100;
            
            // Adjust for rounding error on the last month to ensure exact match
            if ($i == $months) {
                $principalPortion = ceil(round(($principal - $accumulatedPrincipal) * 100, 4)) / 100;
            }

            $totalMonthlyPayment = round($principalPortion + $interestPortion, 2);
            $endingBalance = round($balance - $principalPortion, 2);

            LoanAmortization::create([
                'loan_id' => $loan->id,
                'month_number' => $i,
                'due_date' => $startDate->copy()->addMonths($i),
                'beginning_balance' => $balance,
                'principal_portion' => $principalPortion,
                'interest_portion' => $interestPortion,
                'total_payment' => $totalMonthlyPayment,
                'ending_balance' => max(0, $endingBalance),
            ]);

            $accumulatedPrincipal += $principalPortion;
            $balance = $endingBalance;
        }

        return back()->with('success', 'Loan approved and amortization schedule generated.');
    }

    public function reject(Request $request, Loan $loan)
    {
        $loan->update(['loan_status' => 'rejected']);
        return back()->with('success', 'Loan rejected.');
    }

    public function createPayment(Loan $loan)
    {
        $loan->load('amortizations', 'payments');
        
        $totalScheduled = $loan->amortizations->sum('total_payment');
        $totalPaid = $loan->payments->sum('amount_paid');
        $totalRemainingBalance = max(0, $totalScheduled - $totalPaid);
        
        // Calculate current due dynamically based on schedule
        $totalDueUntilNow = $loan->amortizations->where('due_date', '<=', now()->endOfMonth())->sum('total_payment');
        $currentDue = max(0, $totalDueUntilNow - $totalPaid);
        
        // Calculate penalty: 2% per day of amount due for the month
        $penalty = 0;
        $paidAccumulator = round((float) $totalPaid, 2);
        
        $nextAmortization = null;

        foreach ($loan->amortizations->sortBy('month_number') as $amort) {
            $amortizationDue = round((float) $amort->total_payment, 2);
            
            if (!$nextAmortization && !$amort->is_paid) {
                $nextAmortization = $amort;
            }
            
            if ($paidAccumulator >= $amortizationDue - 0.01) {
                $paidAccumulator -= $amortizationDue;
                continue;
            } else {
                $unpaidPortion = $amortizationDue - max(0, $paidAccumulator);
                $paidAccumulator = 0;
                
                $dueDate = \Carbon\Carbon::parse($amort->due_date)->startOfDay();
                $today = now()->startOfDay();
                
                if ($today->greaterThan($dueDate)) {
                    $daysLate = abs($today->diffInDays($dueDate));
                    if ($daysLate > 0) {
                        $penalty += ($unpaidPortion * 0.02) * $daysLate;
                    }
                }
            }
        }
        
        $penalty = max(0, (int) floor($penalty));
        
        $nextDueDate = $nextAmortization ? \Carbon\Carbon::parse($nextAmortization->due_date)->format('M d, Y') : 'N/A';
        $nextDueAmount = $nextAmortization ? (float) $nextAmortization->total_payment : null;
            
        return view('admin.loans.payments.create', compact('loan', 'totalRemainingBalance', 'currentDue', 'penalty', 'nextDueDate', 'nextDueAmount'));
    }

    public function storePayment(Request $request, Loan $loan)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:1',
            'penalty' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $totalScheduled = (float) $loan->amortizations()->sum('total_payment');
        $totalPaid = (float) $loan->payments()->sum('amount_paid');
        $totalRemainingBalance = max(0, $totalScheduled - $totalPaid);

        if ((float) $request->amount_paid > $totalRemainingBalance + 0.01) {
            return back()
                ->withErrors(['amount_paid' => 'Amount paid cannot be greater than the Total Remaining Balance.'])
                ->withInput();
        }

        $penalty = $request->penalty ?? 0;
        $amountToApply = $request->amount_paid; // Penalty is separate, amount_paid goes purely to principal+interest

        // Current status calculations
        $paidPrincipalSoFar = $loan->payments()->sum('principal_paid');
        $remainingPrincipal = $loan->loan_amount - $paidPrincipalSoFar;

        $interestApplied = 0;
        $principalApplied = 0;

        $unpaidAmortizations = $loan->amortizations()->where('is_paid', false)->orderBy('month_number')->get();

        foreach ($unpaidAmortizations as $amortization) {
            if ($amountToApply <= 0.01) break;

            if ($amountToApply >= $amortization->total_payment - 0.01) {
                // Fully pays this amortization
                $amountToApply -= $amortization->total_payment;
                $interestApplied += $amortization->interest_portion;
                $principalApplied += $amortization->principal_portion;
                $amortization->update(['is_paid' => true]);
            } else {
                // Partially pays: allocate to interest first, then principal. Do not mark as fully paid.
                if ($amountToApply >= $amortization->interest_portion) {
                    $interestApplied += $amortization->interest_portion;
                    $principalApplied += ($amountToApply - $amortization->interest_portion);
                } else {
                    $interestApplied += $amountToApply;
                }
                
                // Adjust the amortization schedule so the totals match the partial payment if the loan gets completed early
                // We'll handle early completion cleanup after the loop
                
                $amountToApply = 0; // Exhausted
            }
        }
        
        // If there's still excess amount (e.g. overpayment beyond schedule), apply to principal
        if ($amountToApply > 0.01) {
            $principalApplied += $amountToApply;
        }

        $newRemaining = $remainingPrincipal - $principalApplied;

        LoanPayment::create([
            'loan_id' => $loan->id,
            'payment_date' => $request->payment_date,
            'amount_paid' => $request->amount_paid,
            'principal_paid' => $principalApplied,
            'interest_paid' => $interestApplied,
            'penalty' => $penalty,
            'remaining_balance_after_payment' => max(0, $newRemaining),
            'received_by' => auth()->id(),
            'payment_method' => $request->payment_method,
            'notes' => $request->notes
        ]);

        $totalScheduled = $loan->amortizations()->sum('total_payment');
        $totalPaidAfter = $loan->payments()->sum('amount_paid');

        if ($totalScheduled - $totalPaidAfter <= 0.01) {
            $loan->update(['loan_status' => 'completed']);
            // Ensure all amortizations are marked paid
            $loan->amortizations()->where('is_paid', false)->update(['is_paid' => true]);
            
            // To ensure perfect matching of totals as requested:
            // Adjust the very last amortization to absorb any rounding differences
            if (abs($totalScheduled - $totalPaidAfter) > 0.01) {
                $lastAmortization = $loan->amortizations()->orderBy('month_number', 'desc')->first();
                if ($lastAmortization) {
                    $difference = $totalPaidAfter - $totalScheduled;
                    $lastAmortization->update([
                        'principal_portion' => max(0, $lastAmortization->principal_portion + $difference),
                        'total_payment' => max(0, $lastAmortization->total_payment + $difference),
                    ]);
                }
            }
        }

        return redirect()->route('admin.loans.show', $loan)->with('success', 'Payment recorded successfully.');
    }

    public function update(Request $request, Loan $loan)
    {
        if ($loan->loan_status !== 'pending') {
            return back()->with('error', 'Only pending loans can be updated.');
        }

        $validated = $request->validate([
            'loan_amount' => 'required|numeric|min:1',
            'term_length_months' => 'required|integer|min:1|max:120',
        ]);

        $loan->update([
            'loan_amount' => $validated['loan_amount'],
            'term_length_months' => $validated['term_length_months'],
        ]);

        return redirect()->route('admin.loans.show', $loan)->with('success', 'Loan updated successfully.');
    }

    public function updatePayment(Request $request, Loan $loan, LoanPayment $payment)
    {
        if ((int) $payment->loan_id !== (int) $loan->id) {
            abort(404);
        }

        if (in_array($loan->loan_status, ['pending', 'rejected'], true)) {
            return back()->with('error', 'Payments cannot be updated for this loan status.');
        }

        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:1',
            'penalty' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $totalScheduled = (float) $loan->amortizations()->sum('total_payment');
        $totalPaid = (float) $loan->payments()->sum('amount_paid');
        $totalPaidExcludingThis = max(0, $totalPaid - (float) $payment->amount_paid);
        $remainingBeforeThisPayment = max(0, $totalScheduled - $totalPaidExcludingThis);

        if ((float) $validated['amount_paid'] > $remainingBeforeThisPayment + 0.01) {
            return back()
                ->withErrors(['amount_paid' => 'Amount paid cannot be greater than the Total Remaining Balance.'])
                ->withInput();
        }

        DB::transaction(function () use ($loan, $payment, $validated) {
            $payment->update([
                'payment_date' => $validated['payment_date'],
                'amount_paid' => $validated['amount_paid'],
                'penalty' => $validated['penalty'] ?? 0,
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $this->recalculateLoanAfterPaymentsChange($loan);
        });

        return redirect()->route('admin.loans.show', $loan)->with('success', 'Payment updated successfully.');
    }

    public function destroyPayment(Request $request, Loan $loan, LoanPayment $payment)
    {
        if ((int) $payment->loan_id !== (int) $loan->id) {
            abort(404);
        }

        if (in_array($loan->loan_status, ['pending', 'rejected'], true)) {
            return back()->with('error', 'Payments cannot be deleted for this loan status.');
        }

        DB::transaction(function () use ($loan, $payment) {
            $payment->delete();
            $this->recalculateLoanAfterPaymentsChange($loan);
        });

        return redirect()->route('admin.loans.show', $loan)->with('success', 'Payment deleted successfully.');
    }

    private function recalculateLoanAfterPaymentsChange(Loan $loan): void
    {
        $loan->amortizations()->update(['is_paid' => false]);

        $amortizations = $loan->amortizations()->orderBy('month_number')->get();
        $payments = $loan->payments()->orderBy('payment_date')->orderBy('id')->get();

        $principalPaidSoFar = 0.0;
        $amortIndex = 0;
        $amortCount = $amortizations->count();

        foreach ($payments as $payment) {
            $amountToApply = (float) $payment->amount_paid;
            $interestApplied = 0.0;
            $principalApplied = 0.0;

            while ($amountToApply > 0.01 && $amortIndex < $amortCount) {
                $amort = $amortizations[$amortIndex];

                if ($amort->is_paid) {
                    $amortIndex++;
                    continue;
                }

                $amortDue = round((float) $amort->total_payment, 2);

                if ($amountToApply >= $amortDue - 0.01) {
                    $amountToApply -= $amortDue;
                    $interestApplied += (float) $amort->interest_portion;
                    $principalApplied += (float) $amort->principal_portion;
                    $amort->update(['is_paid' => true]);
                    $amortIndex++;
                    continue;
                }

                if ($amountToApply >= (float) $amort->interest_portion) {
                    $interestApplied += (float) $amort->interest_portion;
                    $principalApplied += ($amountToApply - (float) $amort->interest_portion);
                } else {
                    $interestApplied += $amountToApply;
                }

                $amountToApply = 0;
            }

            if ($amountToApply > 0.01) {
                $principalApplied += $amountToApply;
            }

            $principalPaidSoFar += $principalApplied;

            $payment->update([
                'principal_paid' => $principalApplied,
                'interest_paid' => $interestApplied,
                'remaining_balance_after_payment' => max(0, (float) $loan->loan_amount - $principalPaidSoFar),
            ]);
        }

        $totalScheduled = (float) $loan->amortizations()->sum('total_payment');
        $totalPaidAfter = (float) $loan->payments()->sum('amount_paid');

        if ($totalScheduled - $totalPaidAfter <= 0.01) {
            $loan->update(['loan_status' => 'completed']);
            $loan->amortizations()->where('is_paid', false)->update(['is_paid' => true]);
            return;
        }

        $hasOverdue = LoanAmortization::query()
            ->where('loan_id', $loan->id)
            ->where('is_paid', false)
            ->whereDate('due_date', '<', now()->toDateString())
            ->exists();

        $loan->update(['loan_status' => $hasOverdue ? 'overdue' : 'active']);
    }
}
