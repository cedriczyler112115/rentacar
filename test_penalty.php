<?php
$loan = \App\Models\Loan::with('amortizations', 'payments')->find(7);
$totalPaid = $loan->payments->sum('amount_paid');
$penalty = 0;
$paidAccumulator = round((float) $totalPaid, 2);
echo "Loan 7: Total Paid = $totalPaid\n";
foreach ($loan->amortizations->sortBy('month_number') as $amort) {
    $amortizationDue = round((float) $amort->total_payment, 2);
    if ($paidAccumulator >= $amortizationDue - 0.01) {
        $paidAccumulator -= $amortizationDue;
        echo "Month {$amort->month_number} (Due: {$amort->due_date}): Fully Paid\n";
    } else {
        $unpaidPortion = $amortizationDue - max(0, $paidAccumulator);
        $paidAccumulator = 0;
        $dueDate = \Carbon\Carbon::parse($amort->due_date)->startOfDay();
        $today = now()->startOfDay();
        echo "Month {$amort->month_number} (Due: {$dueDate->toDateString()}): Unpaid $unpaidPortion\n";
        echo "  Today: {$today->toDateString()}\n";
        echo "  Greater? " . ($today->greaterThan($dueDate) ? 'Yes' : 'No') . "\n";
        if ($today->greaterThan($dueDate)) {
            $daysLate = $today->diffInDays($dueDate);
            echo "  Days Late: $daysLate\n";
            if ($daysLate > 0) {
                $pen = ($unpaidPortion * 0.02) * $daysLate;
                $penalty += $pen;
                echo "  Penalty += $pen\n";
            }
        }
    }
}
echo "Total Penalty: " . max(0, (int) floor($penalty)) . "\n";
