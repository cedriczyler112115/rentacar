<x-member-layout>
    <x-slot name="header">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap: 12px; flex-wrap: wrap;">
            <div>
                <h2>{{ __('My Income') }}</h2>
                <p style="color: #64748b; margin-top: 5px;">View your reported income across your completed rentals and owner bookings.</p>
            </div>
        </div>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px); max-width: none;">
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-top: 20px; box-shadow: var(--shadow-sm);">
            
            <!-- Filters -->
            <form method="GET" action="{{ route('my-income') }}" style="display:flex; gap:14px; flex-wrap:wrap; align-items:flex-end; margin-bottom: 24px;">
                <div style="flex:1; min-width: 160px;">
                    <label style="display:block; font-size: 0.85rem; font-weight: 800; color: #64748b; margin-bottom: 6px;">Year</label>
                    <select name="year" style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px; background: white;">
                        <option value="">All Years</option>
                        @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                            <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                
                <div style="flex:1; min-width: 160px;">
                    <label style="display:block; font-size: 0.85rem; font-weight: 800; color: #64748b; margin-bottom: 6px;">Month</label>
                    <select name="month" style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px; background: white;">
                        <option value="">All Months</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endfor
                    </select>
                </div>
                
                <div style="flex:2; min-width: 200px;">
                    <label style="display:block; font-size: 0.85rem; font-weight: 800; color: #64748b; margin-bottom: 6px;">Vehicle</label>
                    <select name="vehicle_id" style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px; background: white;">
                        <option value="">All Vehicles</option>
                        @foreach($ownedVehicles as $v)
                            <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->name }}{{ $v->license_plate ? ' - '.$v->license_plate : '' }}{{ $v->color ? ' - '.$v->color : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 18px; border-radius: 10px;">Filter</button>
                    <a href="{{ route('my-income') }}" class="btn btn-outline" style="padding: 10px 18px; border-radius: 10px; margin-left: 6px;">Reset</a>
                </div>
            </form>

            <!-- Table -->
            <div style="overflow-x:auto; border: 1px solid #e2e8f0; border-radius: 12px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align:left; background: #0f172a; color: white;">
                            <th style="padding: 14px 16px; font-weight: 800;">Reference</th>
                            <th style="padding: 14px 16px; font-weight: 800;">Travel Date</th>
                            <th style="padding: 14px 16px; font-weight: 800; text-align:center;">Days</th>
                            <th style="padding: 14px 16px; font-weight: 800;">Vehicle</th>
                            <th style="padding: 14px 16px; font-weight: 800;">Destination / Note</th>
                            <th style="padding: 14px 16px; font-weight: 800;">Referral</th>
                            <th style="padding: 14px 16px; font-weight: 800;">Renter</th>
                            <th style="padding: 14px 16px; font-weight: 800; text-align: right;">Estimated Income</th>
                            <th style="padding: 14px 16px; font-weight: 800; text-align: right; color: var(--accent);">Actual Income</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $r)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 14px 16px; font-weight: 900; color: var(--primary);">#{{ str_pad($r->id, 6, '0', STR_PAD_LEFT) }}</td>
                                @php
                                    $from = \Carbon\Carbon::parse($r->datetime_from);
                                    $to = \Carbon\Carbon::parse($r->datetime_to);
                                    $hours = $from->diffInHours($to);
                                    $days = (int) floor($hours / 24);
                                    $extraHours = (int) ($hours % 24);
                                    if ($days === 0 && $extraHours > 0) $days = 1;
                                @endphp
                                <td style="padding: 14px 16px; color:#64748b; font-weight: 700;">
                                    {{ $from->format('M d, Y h:i A') }} → {{ $to->format('M d, Y h:i A') }}
                                </td>
                                <td style="padding: 14px 16px; text-align:center; font-weight: 900; color:#0f172a;">{{ $days > 0 ? $days : 0 }}</td>
                                <td style="padding: 14px 16px; font-weight: 900; color:#0f172a;">
                                    {{ $r->vehicle->name ?? 'N/A' }}
                                    @if($r->vehicle?->license_plate || $r->vehicle?->color)
                                        <div style="margin-top: 4px; color:#64748b; font-weight: 700; font-size: 0.85rem;">
                                            {{ $r->vehicle?->license_plate ?? '—' }}{{ $r->vehicle?->color ? ' • '.$r->vehicle->color : '' }}
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 14px 16px; color:#334155; font-weight: 700;">
                                    @if(strtolower((string)($r->status ?? '')) === 'owner booking')
                                        {{ $r->additional_message ? $r->additional_message : '—' }}
                                    @else
                                        {{ $r->municipality ?? '—' }}{{ $r->province ? ', '.$r->province : '' }}{{ $r->region ? ', '.$r->region : '' }}
                                    @endif
                                </td>
                                <td style="padding: 14px 16px; color:#334155; font-weight: 700;">{{ $r->referral ?? '—' }}</td>
                                <td style="padding: 14px 16px; color:#64748b; font-weight: 700;">{{ $r->user->name ?? 'N/A' }}</td>
                                <td style="padding: 14px 16px; text-align: right; font-weight: 800;">₱{{ number_format($r->estimated_price, 2) }}</td>
                                <td style="padding: 14px 16px; text-align: right; font-weight: 900; color: #10b981;">₱{{ number_format($r->actual_price ?? $r->estimated_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="padding: 40px 16px; text-align:center; color:#64748b; font-weight:700;">No transactions match your filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($totalEstimated > 0 || $totalActual > 0)
                    <tfoot>
                        <tr style="background: rgba(245,158,11,0.08); border-top: 2px solid #e2e8f0;">
                            <td colspan="7" style="padding: 18px 16px; font-weight: 900; color:#0f172a; text-align: right;">OVERALL TOTAL FOR FILTER:</td>
                            <td style="padding: 18px 16px; font-weight: 900; color:#0f172a; text-align: right; font-size: 1.1rem;">₱{{ number_format($totalEstimated, 2) }}</td>
                            <td style="padding: 18px 16px; font-weight: 900; color:var(--accent); text-align: right; font-size: 1.2rem;">₱{{ number_format($totalActual, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            
            <div style="margin-top: 24px;">
                {{ $reports->links() }}
            </div>

        </div>
    </div>
</x-member-layout>
