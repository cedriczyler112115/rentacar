<x-member-layout>
    <x-slot name="header">
        <h2>{{ __('Payment Collection') }}</h2>
        <p style="color: #64748b; margin-top: 5px;">Manage and record payments for all active and overdue loans.</p>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('partials.loan-nav')
            </div>

            <div class="admin-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                <div style="display:flex; align-items:center; justify-content:space-between; gap: 12px; margin-bottom: 14px;">
                    <div style="font-weight: 900; color: var(--primary); font-size: 1.1rem;">Active Loans Collection Hub</div>
                </div>

                @if (session('success'))
                    <div style="margin-bottom: 14px; background: rgba(16,185,129,0.12); color:#065f46; border: 1px solid rgba(16,185,129,0.35); padding: 10px 14px; border-radius: 8px; font-weight: 800;">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-scroll" style="border: 1px solid #e2e8f0; border-radius: 12px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align:left; background: #0f172a; color: white;">
                                <th style="padding: 12px 14px;">Borrower</th>
                                <th style="padding: 12px 14px;">Loan Amount</th>
                                <th style="padding: 12px 14px;">Status</th>
                                <th style="padding: 12px 14px;">Next Payment Due</th>
                                <th style="padding: 12px 14px;">Amount Due</th>
                                <th style="padding: 12px 14px; text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                                @php
                                    $nextAmortization = $loan->amortizations->first();
                                @endphp
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 12px 14px; font-weight: 900; color:#0f172a;">
                                        {{ $loan->borrower_name }}
                                        <div style="font-size: 0.75rem; color: #64748b; font-weight: 800;">Loan #{{ $loan->id }}</div>
                                    </td>
                                    <td style="padding: 12px 14px; font-weight: 800; color:#0f172a;">₱{{ number_format($loan->loan_amount, 2) }}</td>
                                    <td style="padding: 12px 14px;">
                                        <span style="padding: 6px 10px; border-radius: 999px; font-weight: 900; font-size: 0.8rem; border: 1px solid transparent; 
                                            {{ $loan->loan_status === 'active' ? 'background: rgba(16,185,129,0.12); color:#065f46; border-color: rgba(16,185,129,0.35);' : '' }}
                                            {{ $loan->loan_status === 'overdue' ? 'background: rgba(239,68,68,0.12); color:#991b1b; border-color: rgba(239,68,68,0.35);' : '' }}
                                        ">
                                            {{ strtoupper($loan->loan_status) }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px 14px; font-weight: 800; color:#64748b;">
                                        @if($nextAmortization)
                                            {{ \Carbon\Carbon::parse($nextAmortization->due_date)->format('M d, Y') }}
                                            @if(\Carbon\Carbon::parse($nextAmortization->due_date)->isPast())
                                                <span style="color: #dc2626; font-size: 0.75rem; margin-left: 4px;">(Late)</span>
                                            @endif
                                        @else
                                            <span style="color: #16a34a;">Fully Paid</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 14px; font-weight: 900; color:#16a34a;">
                                        @if($nextAmortization)
                                            ₱{{ number_format($nextAmortization->total_payment, 2) }}
                                        @else
                                            ₱0.00
                                        @endif
                                    </td>
                                    <td style="padding: 12px 14px; text-align:right;">
                                        <a href="{{ route('admin.loans.payments.create', $loan) }}" class="btn btn-primary" style="padding: 8px 12px; font-size: 0.9rem;">Record Payment</a>
                                        <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn-outline" style="padding: 8px 12px; font-size: 0.9rem; margin-left: 6px;">Details</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 20px; text-align:center; color:#64748b; font-weight: 800;">No active or overdue loans require payment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-member-layout>
