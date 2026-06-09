@php
    $vehicle = $rental->vehicle;
    $owner = $vehicle?->user;
    $ref = $rental->bookingReference();
    $from = $rental->datetime_from ? $rental->datetime_from->format('M d, Y - h:i A') : '—';
    $to = $rental->datetime_to ? $rental->datetime_to->format('M d, Y - h:i A') : '—';
    $vehicleName = $vehicle?->name ?? 'Vehicle';
    $licensePlate = $vehicle?->license_plate ?? '—';
    $yearModel = $vehicle?->year_model ?? '—';
    $color = $vehicle?->color ?? '—';
    $amount = number_format((float) ($rental->estimated_price ?? 0), 2);
@endphp

<div style="margin:0; padding:0; width:100%; background:#f1f5f9;">
    <div style="max-width: 720px; margin: 0 auto; padding: 24px 14px;">
        <div style="background:#0f172a; border-radius: 14px; padding: 18px 18px; color:#f8fafc;">
            <div style="font-weight: 900; font-size: 1.25rem; letter-spacing: 0.3px;">Auto Amegos Rent-a-Car</div>
            <div style="margin-top: 6px; color:#cbd5e1; font-weight: 700;">Vehicle dispatched</div>
        </div>

        <div style="margin-top: 14px; background:white; border: 1px solid #e2e8f0; border-radius: 14px; overflow:hidden;">
            <div style="padding: 16px 18px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
                <div style="font-weight: 900; color:#0f172a;">Dispatch Reference: {{ $ref }}</div>
                <div style="margin-top: 6px; color:#64748b; font-weight: 800;">Travel Schedule: {{ $from }} → {{ $to }}</div>
            </div>

            <div style="padding: 16px 18px;">
                <div style="background:#f0fdf4; border: 1px solid #dcfce7; border-radius: 12px; padding: 14px;">
                    <div style="font-weight: 900; color:#16a34a;">Dispatch created and confirmed</div>
                    <div style="margin-top: 8px; color:#166534; font-weight: 700;">This vehicle has been dispatched for travel.</div>
                </div>

                <div style="margin-top: 12px; display:grid; grid-template-columns: 1fr; gap: 12px;">
                    <div style="background:#f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px;">
                        <div style="font-weight: 900; color:#0f172a;">Vehicle Details</div>
                        <div style="margin-top: 8px; color:#0f172a; font-weight: 700;">Vehicle: {{ $vehicleName }}</div>
                        <div style="margin-top: 4px; color:#0f172a; font-weight: 700;">Color: {{ $color }}</div>
                        <div style="margin-top: 4px; color:#0f172a; font-weight: 700;">Year Model: {{ $yearModel }}</div>
                        <div style="margin-top: 4px; color:#0f172a; font-weight: 700;">License Plate: {{ $licensePlate }}</div>
                    </div>

                    <div style="background:#f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px;">
                        <div style="font-weight: 900; color:#0f172a;">Travel Details</div>
                        <div style="margin-top: 8px; color:#0f172a; font-weight: 700;">Pickup Location: {{ $rental->pickup_location ?? '—' }}</div>
                        <div style="margin-top: 4px; color:#0f172a; font-weight: 700;">Destination: {{ $rental->municipality ?? '—' }}, {{ $rental->province ?? '—' }}</div>
                        <div style="margin-top: 4px; color:#0f172a; font-weight: 900;">Estimated Amount: ₱{{ $amount }}</div>
                        @if($rental->additional_message)
                            <div style="margin-top: 10px; color:#0f172a; font-weight: 700;">Notes:</div>
                            <div style="margin-top: 6px; color:#334155; white-space: pre-wrap;">{{ $rental->additional_message }}</div>
                        @endif
                    </div>

                    <div style="background:#ffffff; border: 1px dashed rgba(245,158,11,0.55); border-radius: 12px; padding: 14px;">
                        <div style="font-weight: 900; color:#0f172a;">Dispatched By</div>
                        <div style="margin-top: 8px; color:#0f172a; font-weight: 700;">{{ $dispatchedByName }}</div>
                        <div style="margin-top: 4px; color:#0f172a; font-weight: 700;">{{ $dispatchedByEmail }}</div>
                    </div>
                </div>

                <div style="margin-top: 14px;">
                    <a href="{{ url('/admin/dispatching') }}" style="display:inline-block; background:#f59e0b; color:#0f172a; text-decoration:none; font-weight: 900; padding: 12px 16px; border-radius: 10px; border: 1px solid rgba(245,158,11,0.35);">
                        Open Dispatching
                    </a>
                </div>

                <div style="margin-top: 16px; color:#0f172a; font-weight: 700;">
                    Regards,<br>
                    AARAAC Team
                </div>
            </div>
        </div>
    </div>
</div>

