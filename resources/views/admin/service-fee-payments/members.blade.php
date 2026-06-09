<x-member-layout>
    <x-slot name="header">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap: 12px; flex-wrap: wrap;">
            <div>
                <h2>{{ __('Paid / Unpaid Members') }}</h2>
                <p style="color: #64748b; margin-top: 5px;">View which AARACC members paid for a given period.</p>
            </div>
            <a href="{{ route('admin.service-fee-payments.index', request()->only(['year','month'])) }}" class="btn btn-outline" style="padding: 10px 16px; font-size: 0.95rem;">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('admin.partials.nav')
            </div>

            <div class="admin-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                <form id="membersFilters" method="GET" action="{{ route('admin.service-fee-payments.members') }}" style="display:flex; gap: 10px; flex-wrap: wrap; align-items: end;">
                    <div>
                        <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Year</label>
                        <select id="membersYear" name="year" style="width: 160px; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                            @foreach($yearOptions as $y)
                                <option value="{{ $y }}" {{ (int)$year === (int)$y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Month</label>
                        <select id="membersMonth" name="month" style="width: 180px; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                            @foreach($monthNames as $k => $v)
                                <option value="{{ $k }}" {{ (int)$month === (int)$k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Status</label>
                        <select id="membersStatus" name="status" style="width: 160px; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                            <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="unpaid" {{ $status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                    </div>
                </form>

                <div class="table-scroll" style="margin-top: 16px; border: 1px solid #e2e8f0; border-radius: 12px; overflow: auto;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 860px;">
                        <thead>
                            <tr style="text-align:left; background: #0f172a; color: white;">
                                <th style="padding: 12px 14px;">Member</th>
                                <th style="padding: 12px 14px;">Email</th>
                                <th style="padding: 12px 14px;">Vehicles</th>
                                <th style="padding: 12px 14px;">Period</th>
                                <th style="padding: 12px 14px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $u)
                                @php
                                    $isPaid = (int)($u->has_paid ?? 0) > 0;
                                @endphp
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 12px 14px; font-weight: 900; color:#0f172a;">{{ $u->name }}</td>
                                    <td style="padding: 12px 14px; color:#64748b; font-weight: 800;">{{ $u->email }}</td>
                                    <td style="padding: 12px 14px;">
                                        <div style="display:inline-flex; align-items:center; justify-content:center; min-width: 44px; padding: 6px 10px; border-radius: 999px; background: #f8fafc; border: 1px solid #e2e8f0; font-weight: 900; color:#0f172a;">
                                            {{ (int)($u->vehicles_count ?? 0) }}
                                        </div>
                                    </td>
                                    <td style="padding: 12px 14px; font-weight: 900; color:#0f172a;">{{ $monthNames[(int)$month] ?? $month }} {{ $year }}</td>
                                    <td style="padding: 12px 14px;">
                                        @if($isPaid)
                                            <span style="display:inline-flex; align-items:center; gap:8px; padding: 6px 12px; border-radius: 999px; background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; font-weight: 900; font-size: 0.85rem;">
                                                Paid
                                            </span>
                                        @else
                                            <span style="display:inline-flex; align-items:center; gap:8px; padding: 6px 12px; border-radius: 999px; background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; font-weight: 900; font-size: 0.85rem;">
                                                Unpaid
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="padding: 26px; text-align:center; background:#f8fafc;">
                                        <div style="font-weight: 900; color: var(--primary);">No members found</div>
                                        <div style="font-weight: 800; color: #64748b; margin-top: 6px;">Try adjusting your filters.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 16px;">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            if (!window.jQuery) return;
            const $form = $('#membersFilters');
            const $year = $('#membersYear');
            const $month = $('#membersMonth');
            const $status = $('#membersStatus');

            function submitFilters() {
                $form.trigger('submit');
            }

            $month.on('change', submitFilters);
            $status.on('change', submitFilters);
            $year.on('change', submitFilters);
        })();
    </script>
</x-member-layout>
