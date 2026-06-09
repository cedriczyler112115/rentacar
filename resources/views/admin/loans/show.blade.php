<x-member-layout>
    <x-slot name="header">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2>{{ __('Loan Details') }} #{{ $loan->id }}</h2>
                <p style="color: #64748b; margin-top: 5px;">View full details, collateral, and payment history of the borrower.</p>
            </div>
            <a href="{{ route('admin.loans.index') }}" style="font-size:0.9rem; font-weight:800; color:#64748b;">&larr; Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('partials.loan-nav')
            </div>

            <div class="admin-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                @if (session('success'))
                    <div style="margin-bottom: 14px; background: rgba(16,185,129,0.12); color:#065f46; border: 1px solid rgba(16,185,129,0.35); padding: 10px 14px; border-radius: 8px; font-weight: 800;">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div style="margin-bottom: 14px; background: rgba(239,68,68,0.12); color:#991b1b; border: 1px solid rgba(239,68,68,0.35); padding: 10px 14px; border-radius: 8px; font-weight: 800;">
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div style="margin-bottom: 14px; background: rgba(239,68,68,0.12); color:#991b1b; border: 1px solid rgba(239,68,68,0.35); padding: 10px 14px; border-radius: 8px; font-weight: 800;">
                        <ul style="margin:0; padding-left:20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Action Bar -->
                <div style="display:flex; justify-content:space-between; align-items:center; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px; margin-bottom:20px;">
                    <div style="display:flex; align-items:center; gap:16px;">
                        <span style="font-weight:900; color:#475569;">Status:</span>
                        <span style="padding: 6px 12px; border-radius: 999px; font-weight: 900; font-size: 0.85rem; border: 1px solid transparent; 
                            {{ $loan->loan_status === 'active' ? 'background: rgba(16,185,129,0.12); color:#065f46; border-color: rgba(16,185,129,0.35);' : '' }}
                            {{ $loan->loan_status === 'pending' ? 'background: rgba(245,158,11,0.12); color:#92400e; border-color: rgba(245,158,11,0.35);' : '' }}
                            {{ $loan->loan_status === 'completed' ? 'background: rgba(59,130,246,0.12); color:#1e40af; border-color: rgba(59,130,246,0.35);' : '' }}
                            {{ $loan->loan_status === 'rejected' ? 'background: rgba(239,68,68,0.12); color:#991b1b; border-color: rgba(239,68,68,0.35);' : '' }}
                            {{ $loan->loan_status === 'overdue' ? 'background: rgba(239,68,68,0.12); color:#991b1b; border-color: rgba(239,68,68,0.35);' : '' }}
                        ">
                            {{ strtoupper($loan->loan_status) }}
                        </span>
                    </div>

                    <div style="display:flex; gap:10px;">
                        @if($loan->loan_status === 'pending')
                            <form action="{{ route('admin.loans.approve', $loan) }}" method="POST" class="js-loan-approve-form">
                                @csrf
                                <button type="button" class="btn js-loan-approve-btn" style="background:#10b981; color:white; border-color:#059669; padding:8px 16px;">Approve</button>
                            </form>
                            <form action="{{ route('admin.loans.reject', $loan) }}" method="POST" class="js-loan-reject-form">
                                @csrf
                                <button type="button" class="btn js-loan-reject-btn" style="background:#ef4444; color:white; border-color:#dc2626; padding:8px 16px;">Reject</button>
                            </form>
                        @elseif($loan->loan_status === 'active' || $loan->loan_status === 'overdue')
                            <a href="{{ route('admin.loans.payments.create', $loan) }}" class="btn btn-primary" style="padding:8px 16px;">Add Payment</a>
                        @endif
                    </div>
                </div>

                @if($loan->loan_status === 'pending' && request()->boolean('edit'))
                    <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 18px; background: #f8fafc;">
                        <div style="font-weight: 900; color: var(--primary); font-size: 1.05rem; margin-bottom: 12px;">Edit Pending Loan</div>
                        <form action="{{ route('admin.loans.update', $loan) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="loan_amount" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Loan Amount (₱)</label>
                                    <input type="text" name="loan_amount" id="loan_amount" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem; font-weight: 900; background: #ffffff; color: #0f172a;" value="{{ old('loan_amount', $loan->loan_amount) }}" required oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                </div>
                                <div>
                                    <label for="term_length_months" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Term Length (Months)</label>
                                    <input type="number" name="term_length_months" id="term_length_months" min="1" max="120" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem; font-weight: 900; background: #ffffff; color: #0f172a;" value="{{ old('term_length_months', $loan->term_length_months) }}" required>
                                </div>
                            </div>

                            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top: 14px;">
                                <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn-outline" style="padding: 10px 16px;">Cancel</a>
                                <button type="submit" class="btn btn-primary" style="padding: 10px 16px; background: #16a34a; border-color: #15803d;">Save Changes</button>
                            </div>
                        </form>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <!-- Loan Details -->
                    <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                        <h3 style="font-size: 1.05rem; font-weight: 900; color: #0f172a; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 12px;">Loan Data</h3>
                        <table style="width: 100%; font-size: 0.95rem;">
                            <tr><td style="padding:6px 0; color:#64748b; font-weight:800; width:45%;">Principal Amount:</td><td style="padding:6px 0; font-weight:900;">₱{{ number_format($loan->loan_amount, 2) }}</td></tr>
                            <tr><td style="padding:6px 0; color:#64748b; font-weight:800;">Interest Rate:</td><td style="padding:6px 0; font-weight:800;">{{ $loan->interest_rate }}%</td></tr>
                            <tr><td style="padding:6px 0; color:#64748b; font-weight:800;">Term Length:</td><td style="padding:6px 0; font-weight:800;">{{ $loan->term_length_months }} Months</td></tr>
                            <tr><td style="padding:6px 0; color:#64748b; font-weight:800;">Start Date:</td><td style="padding:6px 0; font-weight:800;">{{ $loan->loan_start_date ? \Carbon\Carbon::parse($loan->loan_start_date)->format('M d, Y') : 'N/A' }}</td></tr>
                            <tr><td style="padding:6px 0; color:#64748b; font-weight:800;">Due Date:</td><td style="padding:6px 0; font-weight:800;">{{ $loan->due_date ? \Carbon\Carbon::parse($loan->due_date)->format('M d, Y') : 'N/A' }}</td></tr>
                        </table>
                        
                        @php
                            $totalScheduled = $loan->amortizations->sum('total_payment');
                            $totalPaid = $loan->payments->sum('amount_paid');
                            $remainingBalance = max(0, $totalScheduled - $totalPaid);
                        @endphp
                        <div style="margin-top: 16px; background: rgba(239,68,68,0.05); border: 1px solid rgba(239,68,68,0.2); border-radius: 8px; padding: 12px; display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-weight: 900; color: #b91c1c; font-size: 0.85rem; text-transform: uppercase;">Total Remaining Balance</div>
                            <div style="font-weight: 900; color: #dc2626; font-size: 1.25rem;">₱{{ number_format($remainingBalance, 2) }}</div>
                        </div>
                    </div>

                    <!-- Borrower & Collateral -->
                    <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                        <h3 style="font-size: 1.05rem; font-weight: 900; color: #0f172a; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 12px;">Borrower Profile</h3>
                        <table style="width: 100%; font-size: 0.95rem; margin-bottom: 16px;">
                            <tr><td style="padding:6px 0; color:#64748b; font-weight:800; width:35%;">Name:</td><td style="padding:6px 0; font-weight:900;">{{ $loan->borrower_name }}</td></tr>
                            <tr><td style="padding:6px 0; color:#64748b; font-weight:800;">Type:</td><td style="padding:6px 0; font-weight:900; color: {{ $loan->borrower_type == 'member' ? '#2563eb' : '#0f172a' }}; text-transform: uppercase;">{{ $loan->borrower_type }}</td></tr>
                        </table>

                        @if($loan->borrower_type == 'non-member' && $loan->collaterals->count() > 0)
                            <h4 style="font-size: 0.95rem; font-weight: 900; color: #0f172a; margin-bottom: 8px;">Collateral Info</h4>
                            @foreach($loan->collaterals as $collateral)
                                <div style="background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 8px; font-size: 0.85rem;">
                                    <div style="margin-bottom: 4px;"><span style="font-weight:900; color:#475569;">Type:</span> <span style="font-weight:800;">{{ ucfirst($collateral->collateral_type) }}</span></div>
                                    <div style="margin-bottom: 4px;"><span style="font-weight:900; color:#475569;">Estimated Value:</span> <span style="font-weight:900; color:#16a34a;">₱{{ number_format($collateral->estimated_value, 2) }}</span></div>
                                    <div style="color:#64748b; font-weight:800; margin-top: 6px;">{{ $collateral->collateral_description }}</div>
                                    @if($collateral->proof_of_ownership_path)
                                        <a href="{{ asset('storage/' . $collateral->proof_of_ownership_path) }}" target="_blank" class="btn btn-outline" style="margin-top:10px; padding: 4px 8px; font-size: 0.8rem; display:inline-block;">View Proof Document</a>
                                    @endif
                                </div>
                            @endforeach
                        @endif
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
                    <div id="payment-records" style="font-weight: 900; color: var(--primary); font-size: 1.1rem; margin-bottom: 14px;">Payment Records</div>

                    @php
                        $paymentsTotalScheduled = $loan->amortizations->sum('total_payment');
                        $paymentsTotalPaid = $loan->payments->sum('amount_paid');
                        $paymentsTotalRemainingBalance = max(0, $paymentsTotalScheduled - $paymentsTotalPaid);
                    @endphp

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
                                    <th style="padding: 12px 14px; text-align:right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loan->payments as $payment)
                                    @php
                                        $paidExcludingThisPayment = max(0, $paymentsTotalPaid - $payment->amount_paid);
                                        $remainingBeforeThisPayment = max(0, $paymentsTotalScheduled - $paidExcludingThisPayment);
                                    @endphp
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 12px 14px; font-weight: 800;">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                                        <td style="padding: 12px 14px; font-weight: 900; color:#16a34a;">₱{{ number_format($payment->amount_paid, 2) }}</td>
                                        <td style="padding: 12px 14px; font-weight: 800;">₱{{ number_format($payment->principal_paid, 2) }}</td>
                                        <td style="padding: 12px 14px; font-weight: 800;">₱{{ number_format($payment->interest_paid, 2) }}</td>
                                        <td style="padding: 12px 14px; font-weight: 800; color:#dc2626;">₱{{ number_format($payment->penalty, 2) }}</td>
                                        <td style="padding: 12px 14px; font-weight: 800;">{{ ucfirst($payment->payment_method) }}</td>
                                        <td style="padding: 12px 14px; font-weight: 900;">₱{{ number_format($payment->remaining_balance_after_payment, 2) }}</td>
                                        <td style="padding: 12px 14px; text-align:right; white-space: nowrap;">
                                            <button
                                                type="button"
                                                class="btn btn-outline js-edit-payment"
                                                style="padding: 6px 10px; font-size: 0.85rem;"
                                                data-update-url="{{ route('admin.loans.payments.update', [$loan, $payment]) }}"
                                                data-payment-date="{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}"
                                                data-payment-method="{{ $payment->payment_method }}"
                                                data-amount-paid="{{ number_format($payment->amount_paid, 2, '.', '') }}"
                                                data-penalty="{{ number_format($payment->penalty, 2, '.', '') }}"
                                                data-total-remaining="{{ number_format($remainingBeforeThisPayment, 2, '.', '') }}"
                                                data-notes="{{ base64_encode($payment->notes ?? '') }}"
                                            >Edit</button>
                                            <form action="{{ route('admin.loans.payments.destroy', [$loan, $payment]) }}" method="POST" style="display:inline;" class="js-delete-payment-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn js-delete-payment-btn" style="padding: 6px 10px; font-size: 0.85rem; background:#ef4444; color:white; border-color:#dc2626; margin-left: 6px;">Delete</button>
                                            </form>
                                        </td>
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
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <form id="payment-edit-form" method="POST" style="display:none;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="payment_date" id="payment_edit_payment_date">
                        <input type="hidden" name="payment_method" id="payment_edit_payment_method">
                        <input type="hidden" name="amount_paid" id="payment_edit_amount_paid">
                        <input type="hidden" name="penalty" id="payment_edit_penalty">
                        <input type="hidden" name="notes" id="payment_edit_notes">
                    </form>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function hasJqueryConfirm() {
                return typeof window.jQuery !== 'undefined' && typeof window.jQuery.confirm === 'function';
            }

            function b64DecodeUnicode(str) {
                try {
                    return decodeURIComponent(Array.prototype.map.call(atob(str), function (c) {
                        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
                    }).join(''));
                } catch (e) {
                    try {
                        return atob(str || '');
                    } catch (e2) {
                        return '';
                    }
                }
            }

            var approveBtn = document.querySelector('.js-loan-approve-btn');
            if (approveBtn) {
                approveBtn.addEventListener('click', function () {
                    var form = this.closest('form');
                    if (!form) return;

                    if (hasJqueryConfirm()) {
                        window.jQuery.confirm({
                            title: 'Approve Loan',
                            content: 'Are you sure you want to approve this loan? An amortization schedule will be generated.',
                            type: 'green',
                            buttons: {
                                Approve: {
                                    btnClass: 'btn-green',
                                    action: function () {
                                        form.submit();
                                    }
                                },
                                Cancel: function () {}
                            }
                        });
                    } else {
                        if (confirm('Are you sure you want to approve this loan? An amortization schedule will be generated.')) {
                            form.submit();
                        }
                    }
                });
            }

            var rejectBtn = document.querySelector('.js-loan-reject-btn');
            if (rejectBtn) {
                rejectBtn.addEventListener('click', function () {
                    var form = this.closest('form');
                    if (!form) return;

                    if (hasJqueryConfirm()) {
                        window.jQuery.confirm({
                            title: 'Reject Loan',
                            content: 'Are you sure you want to reject this loan?',
                            type: 'red',
                            buttons: {
                                Reject: {
                                    btnClass: 'btn-red',
                                    action: function () {
                                        form.submit();
                                    }
                                },
                                Cancel: function () {}
                            }
                        });
                    } else {
                        if (confirm('Are you sure you want to reject this loan?')) {
                            form.submit();
                        }
                    }
                });
            }

            document.querySelectorAll('.js-delete-payment-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var form = this.closest('form');
                    if (!form) return;

                    if (hasJqueryConfirm()) {
                        window.jQuery.confirm({
                            title: 'Delete Payment',
                            content: 'Delete this payment record? This will recalculate balances.',
                            type: 'red',
                            buttons: {
                                Delete: {
                                    btnClass: 'btn-red',
                                    action: function () {
                                        form.submit();
                                    }
                                },
                                Cancel: function () {}
                            }
                        });
                    } else {
                        if (confirm('Delete this payment record? This will recalculate balances.')) {
                            form.submit();
                        }
                    }
                });
            });

            document.querySelectorAll('.js-edit-payment').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var updateUrl = this.getAttribute('data-update-url') || '';
                    var paymentDate = this.getAttribute('data-payment-date') || '';
                    var paymentMethod = this.getAttribute('data-payment-method') || 'cash';
                    var amountPaid = this.getAttribute('data-amount-paid') || '';
                    var penalty = this.getAttribute('data-penalty') || '0';
                    var totalRemaining = this.getAttribute('data-total-remaining') || '';
                    var notes = b64DecodeUnicode(this.getAttribute('data-notes') || '');

                    if (!updateUrl) return;

                    var contentHtml =
                        '<form id="edit-payment-modal-form" style="margin-top: 6px;">' +
                            '<div style="margin-bottom: 10px; display:flex; justify-content:space-between; align-items:center; background: rgba(59,130,246,0.05); border: 1px solid rgba(59,130,246,0.2); border-radius: 8px; padding: 10px 12px;">' +
                                '<span style="font-weight: 900; color:#1e40af; font-size: 0.85rem; text-transform: uppercase;">Total Remaining Balance</span>' +
                                '<span style="font-weight: 900; color:#1d4ed8;">₱ ' + (parseFloat(totalRemaining || '0').toFixed(2)) + '</span>' +
                            '</div>' +
                            '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">' +
                                '<div>' +
                                    '<label style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Payment Date</label>' +
                                    '<input type="date" id="m_payment_date" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #ffffff; color: #0f172a;" required value="' + paymentDate + '">' +
                                '</div>' +
                                '<div>' +
                                    '<label style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Payment Method</label>' +
                                    '<select id="m_payment_method" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #ffffff; color: #0f172a;" required>' +
                                        '<option value="cash">Cash</option>' +
                                        '<option value="gcash">GCash</option>' +
                                        '<option value="bank_transfer">Bank Transfer</option>' +
                                        '<option value="cheque">Cheque</option>' +
                                    '</select>' +
                                '</div>' +
                                '<div>' +
                                    '<label style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Amount Paid (₱)</label>' +
                                    '<input type="text" id="m_amount_paid" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1.05rem; font-weight: 900; background: #ffffff; color: #15803d;" required value="' + amountPaid + '">' +
                                '</div>' +
                                '<div>' +
                                    '<label style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Penalty (₱)</label>' +
                                    '<input type="text" id="m_penalty" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #ffffff; color: #b91c1c;" value="' + penalty + '">' +
                                '</div>' +
                                '<div class="md:col-span-2">' +
                                    '<label style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Remarks / Notes / Reference No.</label>' +
                                    '<textarea id="m_notes" rows="2" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #ffffff; color: #0f172a;"></textarea>' +
                                '</div>' +
                            '</div>' +
                        '</form>';

                    if (hasJqueryConfirm()) {
                        window.jQuery.confirm({
                            title: 'Edit Payment',
                            content: contentHtml,
                            type: 'blue',
                            columnClass: 'col-md-8 col-md-offset-2',
                            onContentReady: function () {
                                var $content = this.$content;
                                $content.find('#m_payment_method').val(paymentMethod);
                                $content.find('#m_notes').val(notes);
                                $content.find('#m_amount_paid, #m_penalty').on('input', function () {
                                    this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
                                });
                            },
                            buttons: {
                                Save: {
                                    btnClass: 'btn-green',
                                    action: function () {
                                        var $c = this.$content;
                                        var d = $c.find('#m_payment_date').val();
                                        var m = $c.find('#m_payment_method').val();
                                        var a = $c.find('#m_amount_paid').val();
                                        var p = $c.find('#m_penalty').val();
                                        var n = $c.find('#m_notes').val();

                                        if (!d || !m || !a) {
                                            return false;
                                        }

                                        var maxAllowed = parseFloat(totalRemaining || '0') || 0;
                                        var newAmount = parseFloat(a || '0') || 0;
                                        if (newAmount > (maxAllowed + 0.01)) {
                                            window.jQuery.alert({
                                                title: 'Invalid Amount',
                                                content: 'Amount paid cannot be greater than the Total Remaining Balance.',
                                                type: 'red',
                                            });
                                            return false;
                                        }

                                        var form = document.getElementById('payment-edit-form');
                                        if (!form) return false;

                                        form.setAttribute('action', updateUrl);
                                        document.getElementById('payment_edit_payment_date').value = d;
                                        document.getElementById('payment_edit_payment_method').value = m;
                                        document.getElementById('payment_edit_amount_paid').value = a;
                                        document.getElementById('payment_edit_penalty').value = p || 0;
                                        document.getElementById('payment_edit_notes').value = n || '';
                                        form.submit();
                                    }
                                },
                                Cancel: function () {}
                            }
                        });
                    } else {
                        alert('Edit requires the confirmation modal library to be loaded.');
                    }
                });
            });
        });
    </script>
</x-member-layout>
