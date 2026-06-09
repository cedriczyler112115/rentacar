<x-member-layout>
    <x-slot name="header">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2>{{ __('Record Payment for Loan') }} #{{ $loan->id }}</h2>
                <p style="color: #64748b; margin-top: 5px;">Process and record a new payment for the borrower.</p>
            </div>
            <a href="{{ route('admin.loans.show', $loan) }}" style="font-size:0.9rem; font-weight:800; color:#64748b;">&larr; Back to Loan Details</a>
        </div>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('partials.loan-nav')
            </div>

            <div class="admin-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                <div style="font-weight: 900; color: var(--primary); font-size: 1.1rem; margin-bottom: 14px;">Payment Entry Form</div>
                
                @if ($errors->any())
                    <div style="margin-bottom: 14px; background: rgba(239,68,68,0.12); color:#991b1b; border: 1px solid rgba(239,68,68,0.35); padding: 10px 14px; border-radius: 8px; font-weight: 800;">
                        <ul style="margin:0; padding-left:20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @php
                    $totalDue = (float) $currentDue + (float) ($penalty ?? 0);
                @endphp

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                    <div style="background: rgba(59,130,246,0.05); border: 1px solid rgba(59,130,246,0.2); border-radius: 8px; padding: 16px;">
                        <div style="font-weight: 900; color: #1e40af; font-size: 0.85rem; text-transform: uppercase;">Total Remaining Balance</div>
                        <div style="font-weight: 900; color: #1d4ed8; font-size: 1.25rem; margin-top: 4px;">₱{{ number_format($totalRemainingBalance, 2) }}</div>
                    </div>
                    <div style="background: rgba(245,158,11,0.05); border: 1px solid rgba(245,158,11,0.2); border-radius: 8px; padding: 16px;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <div style="font-weight: 900; color: #92400e; font-size: 0.85rem; text-transform: uppercase;">Total Due (Current Due + Penalty)</div>
                            @if(isset($nextDueDate) && $nextDueDate !== 'N/A')
                                <div style="font-weight: 800; color: #b45309; font-size: 0.8rem; background: rgba(245,158,11,0.15); padding: 2px 8px; border-radius: 4px;">Due: {{ $nextDueDate }}@if(isset($nextDueAmount)) (<b>₱{{ number_format($nextDueAmount, 2) }}</b>)@endif</div>
                            @endif
                        </div>
                        <div style="font-weight: 900; color: #b45309; font-size: 1.25rem; margin-top: 4px;">₱{{ number_format($totalDue, 2) }}</div>
                        <div style="display:flex; justify-content:space-between; margin-top: 6px; font-size: 0.9rem;">
                            <span style="color:#64748b; font-weight: 800;">Current Due</span>
                            <span style="font-weight: 900; color:#0f172a;">₱{{ number_format($currentDue, 2) }}</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-top: 4px; font-size: 0.9rem;">
                            <span style="color:#64748b; font-weight: 800;">Penalty</span>
                            <span style="font-weight: 900; color:#b91c1c;">₱{{ number_format((float) ($penalty ?? 0), 2) }}</span>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 20px; font-size: 0.85rem; color: #64748b; font-weight: 800;">
                    Next unpaid amortizations are automatically settled and merged from the entered payment amount.
                </div>

                <form id="payment-form" action="{{ route('admin.loans.payments.store', $loan) }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label for="payment_date" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Payment Date</label>
                            <input type="date" name="payment_date" id="payment_date" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #0f172a;" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                        </div>

                        <div>
                            <label for="payment_method" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Payment Method</label>
                            <select name="payment_method" id="payment_method" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #0f172a;" required>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="gcash" {{ old('payment_method') == 'gcash' ? 'selected' : '' }}>GCash</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                            </select>
                        </div>

                        <div>
                            <label for="amount_paid" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Amount Paid (₱)</label>
                            <input type="text" name="amount_paid" id="amount_paid" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1.1rem; font-weight: 900; background: #f8fafc; color: #15803d;" value="{{ old('amount_paid', $currentDue > 0 ? $currentDue : '') }}" required oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                        </div>

                        <div>
                            <label for="penalty" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Penalty of 2% of the amount due per day if late payment (₱)</label> 
                            <input type="text" name="penalty" id="penalty" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #e2e8f0; color: #b91c1c; cursor: not-allowed;" value="{{ old('penalty', isset($penalty) ? number_format($penalty, 0, '.', '') : 0) }}" readonly>
                        </div>

                        <div class="md:col-span-2">
                            <label for="notes" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Remarks / Notes / Reference No.</label>
                            <textarea name="notes" id="notes" rows="2" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #0f172a;">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                        <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn-outline" style="padding: 10px 16px;">Cancel</a>
                        <button type="button" id="btn-submit" class="btn btn-primary" style="padding: 10px 16px; background: #16a34a; border-color: #15803d;">Record Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var totalRemainingBalance = {{ json_encode((float) $totalRemainingBalance) }};
            var btn = document.getElementById('btn-submit');
            if (btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    var form = document.getElementById('payment-form');
                    if (!form.reportValidity()) {
                        return;
                    }
                    
                    var amountPaidInput = document.getElementById('amount_paid');
                    var penaltyInput = document.getElementById('penalty');
                    
                    var amountPaid = parseFloat(amountPaidInput.value) || 0;
                    var penalty = parseFloat(penaltyInput.value) || 0;
                    var totalAmount = amountPaid + penalty;

                    if (amountPaid > (parseFloat(totalRemainingBalance) + 0.01)) {
                        if (typeof window.jQuery !== 'undefined' && typeof window.jQuery.alert === 'function') {
                            window.jQuery.alert({
                                title: 'Invalid Amount',
                                content: 'Amount paid cannot be greater than the Total Remaining Balance.',
                                type: 'red',
                            });
                        } else {
                            alert('Amount paid cannot be greater than the Total Remaining Balance.');
                        }
                        return;
                    }
                    
                    if (typeof window.jQuery !== 'undefined' && typeof window.jQuery.confirm !== 'undefined') {
                        window.jQuery.confirm({
                            title: 'Confirm Payment',
                            content: '<div style="margin-bottom: 10px;">Please confirm the payment details:</div>' +
                                     '<div style="display: flex; justify-content: space-between; margin-bottom: 5px;">' +
                                     '<span style="color: #64748b;">Amount Paid:</span>' +
                                     '<strong style="color: #15803d;">₱ ' + amountPaid.toFixed(2) + '</strong>' +
                                     '</div>' +
                                     '<div style="display: flex; justify-content: space-between; margin-bottom: 5px;">' +
                                     '<span style="color: #64748b;">Penalty Amount:</span>' +
                                     '<strong style="color: #b91c1c;">₱ ' + penalty.toFixed(2) + '</strong>' +
                                     '</div>' +
                                     '<hr style="margin: 10px 0; border-color: #cbd5e1;">' +
                                     '<div style="display: flex; justify-content: space-between; font-size: 1.1rem;">' +
                                     '<strong style="color: #0f172a;">Total Expected:</strong>' +
                                     '<strong style="color: #0f172a;">₱ ' + totalAmount.toFixed(2) + '</strong>' +
                                     '</div>',
                            type: 'green',
                            theme: 'modern',
                            icon: 'fa fa-money',
                            buttons: {
                                confirm: {
                                    text: 'Confirm & Record',
                                    btnClass: 'btn-green',
                                    action: function () {
                                        form.submit();
                                    }
                                },
                                cancel: {
                                    text: 'Cancel',
                                    action: function () {}
                                }
                            }
                        });
                    } else {
                        var msg = "Confirm Payment?\n\nAmount Paid: ₱ " + amountPaid.toFixed(2) + 
                                  "\nPenalty Amount: ₱ " + penalty.toFixed(2) + 
                                  "\n\nTotal Expected: ₱ " + totalAmount.toFixed(2);
                        if (confirm(msg)) {
                            form.submit();
                        }
                    }
                });
            }
        });
    </script>
</x-member-layout>
