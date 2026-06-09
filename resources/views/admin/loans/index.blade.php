<x-member-layout>
    <x-slot name="header">
        <h2>{{ __('Loan Management Dashboard') }}</h2>
        <p style="color: #64748b; margin-top: 5px;">Manage active loans, approvals, and collections.</p>
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

                <!-- Analytics Overview -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                        <h3 style="color: #64748b; font-size: 0.85rem; font-weight: 800; text-transform: uppercase;">Active Loans</h3>
                        <p style="font-size: 1.8rem; font-weight: 900; color: #0f172a; margin-top: 4px;">{{ $totalActiveLoans }}</p>
                    </div>
                    <div style="background: rgba(59,130,246,0.05); border: 1px solid rgba(59,130,246,0.2); border-radius: 12px; padding: 16px;">
                        <h3 style="color: #1e40af; font-size: 0.85rem; font-weight: 800; text-transform: uppercase;">Total Member Capital</h3>
                        <p style="font-size: 1.8rem; font-weight: 900; color: #1d4ed8; margin-top: 4px;">₱{{ number_format($totalMemberCapitalAccumulated ?? 0, 2) }}</p>
                    </div>

                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                        <h3 style="color: #64748b; font-size: 0.85rem; font-weight: 800; text-transform: uppercase;">Total Released</h3>
                        <p style="font-size: 1.8rem; font-weight: 900; color: #2563eb; margin-top: 4px;">₱{{ number_format($totalReleasedAmount, 2) }}</p>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                        <h3 style="color: #64748b; font-size: 0.85rem; font-weight: 800; text-transform: uppercase;">Total Collected</h3>
                        <p style="font-size: 1.8rem; font-weight: 900; color: #16a34a; margin-top: 4px;">₱{{ number_format($totalCollectedPayments, 2) }}</p>
                    </div>
                    <div style="background: #fdf2f8; border: 1px solid #fbcfe8; border-radius: 12px; padding: 16px;">
                        <h3 style="color: #9d174d; font-size: 0.85rem; font-weight: 800; text-transform: uppercase;">Penalties Collected</h3>
                        <p style="font-size: 1.8rem; font-weight: 900; color: #be185d; margin-top: 4px;">₱{{ number_format($totalCollectedPenalties, 2) }}</p>
                    </div>
                    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px;">
                        <h3 style="color: #991b1b; font-size: 0.85rem; font-weight: 800; text-transform: uppercase;">Overdue Loans</h3>
                        <p style="font-size: 1.8rem; font-weight: 900; color: #dc2626; margin-top: 4px;">{{ $overdueLoans }}</p>
                    </div>
                </div>

                <div style="font-weight: 900; color: var(--primary); font-size: 1.1rem; margin-bottom: 14px;">All Loans</div>

                <div class="table-scroll" style="border: 1px solid #e2e8f0; border-radius: 12px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align:left; background: #0f172a; color: white;">
                                <th style="padding: 12px 14px;">ID</th>
                                <th style="padding: 12px 14px;">Borrower</th>
                                <th style="padding: 12px 14px;">Type</th>
                                <th style="padding: 12px 14px;">Amount</th>
                                <th style="padding: 12px 14px;">Term (Months)</th>
                                <th style="padding: 12px 14px;">Rate</th>
                                <th style="padding: 12px 14px;">Status</th>
                                <th style="padding: 12px 14px; text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 12px 14px; font-weight: 800; color:#64748b;">#{{ $loan->id }}</td>
                                    <td style="padding: 12px 14px; font-weight: 900; color:#0f172a;">{{ $loan->borrower_name }}</td>
                                    <td style="padding: 12px 14px; font-weight: 800;">
                                        @if($loan->borrower_type == 'member')
                                            <span style="color: #2563eb;">Member</span>
                                        @else
                                            <span style="color: #64748b;">Non-Member</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 14px; font-weight: 900; color:#0f172a;">₱{{ number_format($loan->loan_amount, 2) }}</td>
                                    <td style="padding: 12px 14px; font-weight: 900; color:#0f172a;">{{ (int) $loan->term_length_months }} months</td>
                                    <td style="padding: 12px 14px; font-weight: 800;">{{ $loan->interest_rate }}%</td>
                                    <td style="padding: 12px 14px;">
                                        <span style="padding: 6px 10px; border-radius: 999px; font-weight: 900; font-size: 0.8rem; border: 1px solid transparent; 
                                            {{ $loan->loan_status === 'active' ? 'background: rgba(16,185,129,0.12); color:#065f46; border-color: rgba(16,185,129,0.35);' : '' }}
                                            {{ $loan->loan_status === 'pending' ? 'background: rgba(245,158,11,0.12); color:#92400e; border-color: rgba(245,158,11,0.35);' : '' }}
                                            {{ $loan->loan_status === 'completed' ? 'background: rgba(59,130,246,0.12); color:#1e40af; border-color: rgba(59,130,246,0.35);' : '' }}
                                            {{ $loan->loan_status === 'rejected' ? 'background: rgba(239,68,68,0.12); color:#991b1b; border-color: rgba(239,68,68,0.35);' : '' }}
                                            {{ $loan->loan_status === 'overdue' ? 'background: rgba(239,68,68,0.12); color:#991b1b; border-color: rgba(239,68,68,0.35);' : '' }}
                                        ">
                                            {{ strtoupper($loan->loan_status) }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px 14px; text-align:right;">
                                        @if($loan->loan_status === 'pending')
                                            <a href="{{ route('admin.loans.show', $loan) }}?edit=1" class="btn btn-primary" style="padding: 8px 12px; font-size: 0.9rem;">Edit</a>
                                        @endif
                                        <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn-outline" style="padding: 8px 12px; font-size: 0.9rem;">Manage</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="padding: 20px; text-align:center; color:#64748b; font-weight: 800;">No loans found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-member-layout>
