<x-member-layout>
    <x-slot name="header">
        <h2>{{ __('Loan Details') }} #{{ $loan->id }}</h2>
        <p style="color: #64748b; margin-top: 5px;">View full details and payment history of your loan.</p>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('partials.loan-nav')
            </div>

            <div class="admin-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                <div style="font-weight: 900; color: var(--primary); font-size: 1.1rem; margin-bottom: 14px;">Loan Details</div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                        <h3 style="color: #64748b; font-size: 0.85rem; font-weight: 800; text-transform: uppercase;">Principal Amount</h3>
                        <p style="font-size: 1.4rem; font-weight: 900; color: #0f172a; margin-top: 4px;">₱{{ number_format($loan->loan_amount, 2) }}</p>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                        <h3 style="color: #64748b; font-size: 0.85rem; font-weight: 800; text-transform: uppercase;">Interest Rate</h3>
                        <p style="font-size: 1.4rem; font-weight: 900; color: #0f172a; margin-top: 4px;">{{ $loan->interest_rate }}%</p>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                        <h3 style="color: #64748b; font-size: 0.85rem; font-weight: 800; text-transform: uppercase;">Status</h3>
                        <p style="font-size: 1.4rem; font-weight: 900; color: var(--accent); margin-top: 4px; text-transform: uppercase;">{{ $loan->loan_status }}</p>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                        <h3 style="color: #64748b; font-size: 0.85rem; font-weight: 800; text-transform: uppercase;">Term Length</h3>
                        <p style="font-size: 1.4rem; font-weight: 900; color: #0f172a; margin-top: 4px;">{{ $loan->term_length_months }} Months</p>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                        <h3 style="color: #64748b; font-size: 0.85rem; font-weight: 800; text-transform: uppercase;">Start Date</h3>
                        <p style="font-size: 1.2rem; font-weight: 900; color: #0f172a; margin-top: 4px;">{{ $loan->loan_start_date ? \Carbon\Carbon::parse($loan->loan_start_date)->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                        <h3 style="color: #64748b; font-size: 0.85rem; font-weight: 800; text-transform: uppercase;">Due Date</h3>
                        <p style="font-size: 1.2rem; font-weight: 900; color: #0f172a; margin-top: 4px;">{{ $loan->due_date ? \Carbon\Carbon::parse($loan->due_date)->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    @if($loan->coMaker)
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                            <h3 style="color: #64748b; font-size: 0.85rem; font-weight: 800; text-transform: uppercase;">Co-Maker</h3>
                            <p style="font-size: 1.2rem; font-weight: 900; color: #0f172a; margin-top: 4px;">{{ $loan->coMaker->name }}</p>
                        </div>
                    @endif
                    @php
                        $totalScheduled = $loan->amortizations->sum('total_payment');
                        $totalPaid = $loan->payments->sum('amount_paid');
                        $remainingBalance = max(0, $totalScheduled - $totalPaid);
                    @endphp
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                        <h3 style="color: #64748b; font-size: 0.85rem; font-weight: 800; text-transform: uppercase;">Total Remaining</h3>
                        <p style="font-size: 1.4rem; font-weight: 900; color: #dc2626; margin-top: 4px;">₱{{ number_format($remainingBalance, 2) }}</p>
                    </div>
                </div>

                @if($loan->amortizations->count() > 0)
                    <div style="font-weight: 900; color: var(--primary); font-size: 1.1rem; margin-bottom: 14px; margin-top: 24px;">Amortization Schedule</div>
                    <div class="table-scroll" style="border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 30px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="text-align:left; background: #0f172a; color: white;">
                                    <th style="padding: 12px 14px;">Month</th>
                                    <th style="padding: 12px 14px;">Due Date</th>
                                    <th style="padding: 12px 14px;">Beg. Balance</th>
                                    <th style="padding: 12px 14px;">Principal</th>
                                    <th style="padding: 12px 14px;">Interest</th>
                                    <th style="padding: 12px 14px;">Total Payment</th>
                                    <th style="padding: 12px 14px;">End Balance</th>
                                    <th style="padding: 12px 14px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $cumulativePaid = $loan->payments->sum('amount_paid');
                                @endphp
                                @foreach($loan->amortizations as $amortization)
                                    @php
                                        $status = 'UNPAID';
                                        $remainingForThis = $amortization->total_payment;
                                        if ($cumulativePaid >= $amortization->total_payment - 0.01) {
                                            $status = 'PAID';
                                            $cumulativePaid -= $amortization->total_payment;
                                            $remainingForThis = 0;
                                        } elseif ($cumulativePaid > 0.01) {
                                            $status = 'PARTIAL';
                                            $remainingForThis -= $cumulativePaid;
                                            $cumulativePaid = 0;
                                        }
                                    @endphp
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 12px 14px; font-weight: 900;">{{ $amortization->month_number }}</td>
                                        <td style="padding: 12px 14px; font-weight: 800;">{{ \Carbon\Carbon::parse($amortization->due_date)->format('M d, Y') }}</td>
                                        <td style="padding: 12px 14px; font-weight: 800;">₱{{ number_format($amortization->beginning_balance, 2) }}</td>
                                        <td style="padding: 12px 14px; font-weight: 800; color:#16a34a;">₱{{ number_format($amortization->principal_portion, 2) }}</td>
                                        <td style="padding: 12px 14px; font-weight: 800; color:#dc2626;">₱{{ number_format($amortization->interest_portion, 2) }}</td>
                                        <td style="padding: 12px 14px; font-weight: 900;">₱{{ number_format($amortization->total_payment, 2) }}</td>
                                        <td style="padding: 12px 14px; font-weight: 800;">₱{{ number_format($amortization->ending_balance, 2) }}</td>
                                        <td style="padding: 12px 14px;">
                                            @if($status === 'PAID')
                                                <span style="background: rgba(16,185,129,0.12); color:#065f46; border: 1px solid rgba(16,185,129,0.35); padding: 4px 8px; border-radius: 999px; font-weight: 900; font-size: 0.75rem;">PAID</span>
                                            @elseif($status === 'PARTIAL')
                                                <span style="background: rgba(59,130,246,0.12); color:#1d4ed8; border: 1px solid rgba(59,130,246,0.35); padding: 4px 8px; border-radius: 999px; font-weight: 900; font-size: 0.75rem;">PARTIAL (₱{{ number_format($remainingForThis, 2) }} BAL)</span>
                                            @else
                                                <span style="background: rgba(245,158,11,0.12); color:#92400e; border: 1px solid rgba(245,158,11,0.35); padding: 4px 8px; border-radius: 999px; font-weight: 900; font-size: 0.75rem;">UNPAID</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot style="background: #f1f5f9; font-weight: 900; color: #0f172a; border-top: 2px solid #cbd5e1;">
                                <tr>
                                    <td colspan="3" style="padding: 12px 14px; text-align: right;">TOTAL:</td>
                                    <td style="padding: 12px 14px; color:#16a34a;">₱{{ number_format(ceil($loan->amortizations->sum('principal_portion') * 100) / 100, 2) }}</td>
                                    <td style="padding: 12px 14px; color:#dc2626;">₱{{ number_format(ceil($loan->amortizations->sum('interest_portion') * 100) / 100, 2) }}</td>
                                    <td style="padding: 12px 14px;">₱{{ number_format(ceil($loan->amortizations->sum('total_payment') * 100) / 100, 2) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif

                @if($loan->payments->count() > 0)
                    <div style="font-weight: 900; color: var(--primary); font-size: 1.1rem; margin-bottom: 14px;">Payment Records</div>
                    <div class="table-scroll" style="border: 1px solid #e2e8f0; border-radius: 12px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="text-align:left; background: #0f172a; color: white;">
                                    <th style="padding: 12px 14px;">Date</th>
                                    <th style="padding: 12px 14px;">Amount Paid</th>
                                    <th style="padding: 12px 14px;">Principal</th>
                                    <th style="padding: 12px 14px;">Interest</th>
                                    <th style="padding: 12px 14px;">Penalty</th>
                                    <th style="padding: 12px 14px;">Method</th>
                                    <th style="padding: 12px 14px;">Remaining Bal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loan->payments as $payment)
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 12px 14px; font-weight: 800;">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                                        <td style="padding: 12px 14px; font-weight: 900; color:#16a34a;">₱{{ number_format($payment->amount_paid, 2) }}</td>
                                        <td style="padding: 12px 14px; font-weight: 800;">₱{{ number_format($payment->principal_paid, 2) }}</td>
                                        <td style="padding: 12px 14px; font-weight: 800;">₱{{ number_format($payment->interest_paid, 2) }}</td>
                                        <td style="padding: 12px 14px; font-weight: 800; color:#dc2626;">₱{{ number_format($payment->penalty, 2) }}</td>
                                        <td style="padding: 12px 14px; font-weight: 800;">{{ ucfirst($payment->payment_method) }}</td>
                                        <td style="padding: 12px 14px; font-weight: 900;">₱{{ number_format($payment->remaining_balance_after_payment, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot style="background: #f1f5f9; font-weight: 900; color: #0f172a; border-top: 2px solid #cbd5e1;">
                                <tr>
                                    <td style="padding: 12px 14px; text-align: right;">TOTAL:</td>
                                    <td style="padding: 12px 14px; color:#16a34a;">₱{{ number_format($loan->payments->sum('amount_paid'), 2) }}</td>
                                    <td style="padding: 12px 14px;">₱{{ number_format($loan->payments->sum('principal_paid'), 2) }}</td>
                                    <td style="padding: 12px 14px;">₱{{ number_format($loan->payments->sum('interest_paid'), 2) }}</td>
                                    <td style="padding: 12px 14px; color:#dc2626;">₱{{ number_format($loan->payments->sum('penalty'), 2) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-member-layout>
