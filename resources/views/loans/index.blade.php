<x-member-layout>
    <x-slot name="header">
        <h2>{{ __('My Loans') }}</h2>
        <p style="color: #64748b; margin-top: 5px;">View and manage your active and past loans.</p>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('partials.loan-nav')
            </div>

            <div class="admin-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                <div style="display:flex; align-items:center; justify-content:space-between; gap: 12px; margin-bottom: 14px;">
                    <div style="font-weight: 900; color: var(--primary); font-size: 1.1rem;">My Loans</div>
                    <a href="{{ route('loans.create') }}" class="btn btn-primary" style="padding: 10px 16px; font-size: 0.95rem;">Apply for a Loan</a>
                </div>

                @if (session('success'))
                    <div style="margin-bottom: 14px; background: rgba(16,185,129,0.12); color:#065f46; border: 1px solid rgba(16,185,129,0.35); padding: 10px 14px; border-radius: 8px; font-weight: 800;">
                        {{ session('success') }}
                    </div>
                @endif

                @if($loans->isEmpty())
                    <p style="color: #64748b; font-weight: 800; text-align: center; padding: 20px;">You don't have any loans yet.</p>
                @else
                    <div class="table-scroll" style="border: 1px solid #e2e8f0; border-radius: 12px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="text-align:left; background: #0f172a; color: white;">
                                    <th style="padding: 12px 14px;">Amount</th>
                                    <th style="padding: 12px 14px;">Rate</th>
                                    <th style="padding: 12px 14px;">Term</th>
                                    <th style="padding: 12px 14px;">Status</th>
                                    <th style="padding: 12px 14px;">Applied On</th>
                                    <th style="padding: 12px 14px; text-align:right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loans as $loan)
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 12px 14px; font-weight: 900; color:#0f172a;">₱{{ number_format($loan->loan_amount, 2) }}</td>
                                        <td style="padding: 12px 14px; font-weight: 800; color:#0f172a;">{{ $loan->interest_rate }}%</td>
                                        <td style="padding: 12px 14px; font-weight: 800; color:#0f172a;">{{ $loan->term_length_months }} mo</td>
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
                                        <td style="padding: 12px 14px; font-weight: 800; color:#64748b;">{{ $loan->created_at->format('M d, Y') }}</td>
                                        <td style="padding: 12px 14px; text-align:right;">
                                            <a href="{{ route('loans.show', $loan) }}" class="btn btn-outline" style="padding: 8px 12px; font-size: 0.9rem;">View Details</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-member-layout>
