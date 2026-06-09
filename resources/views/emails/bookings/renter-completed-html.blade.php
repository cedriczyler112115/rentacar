@php
    $vehicle = $rental->vehicle;
    $ref = $rental->bookingReference();
    $from = $rental->datetime_from ? $rental->datetime_from->format('M d, Y - h:i A') : '—';
    $to = $rental->datetime_to ? $rental->datetime_to->format('M d, Y - h:i A') : '—';
    $vehicleName = $vehicle?->name ?? 'Vehicle';
    $amount = number_format((float) ($rental->estimated_price ?? 0), 2);
    $hours = $rental->datetime_from && $rental->datetime_to ? $rental->datetime_from->diffInHours($rental->datetime_to) : 0;
    $days = $hours > 0 ? max(1, (int) floor($hours / 24)) : 0;
@endphp

<div style="margin:0; padding:0; width:100%; background:#f1f5f9;">
    <div style="max-width: 720px; margin: 0 auto; padding: 24px 14px;">
        <div style="background:#0f172a; border-radius: 14px; padding: 18px 18px; color:#f8fafc;">
            <div style="font-weight: 900; font-size: 1.25rem; letter-spacing: 0.3px;">Auto Amegos Rent-a-Car</div>
            <div style="margin-top: 6px; color:#cbd5e1; font-weight: 700;">Travel completed</div>
        </div>

        <div style="margin-top: 14px; background:white; border: 1px solid #e2e8f0; border-radius: 14px; overflow:hidden;">
            <div style="padding: 16px 18px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
                <div style="font-weight: 900; color:#0f172a;">Booking Reference: {{ $ref }}</div>
                <div style="margin-top: 6px; color:#64748b; font-weight: 800;">Rental Period: {{ $from }} → {{ $to }}</div>
            </div>

            <div style="padding: 16px 18px;">
                <div style="background:#eff6ff; border: 1px solid #dbeafe; border-radius: 12px; padding: 14px;">
                    <div style="font-weight: 900; color:#1d4ed8;">Trip marked as completed</div>
                    <div style="margin-top: 8px; color:#1e40af; font-weight: 700;">Thank you for choosing Auto Amegos Rent-a-Car.</div>
                </div>

                <div style="margin-top: 12px; display:grid; grid-template-columns: 1fr; gap: 12px;">
                    <div style="background:#f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px;">
                        <div style="font-weight: 900; color:#0f172a;">Final Summary</div>
                        <div style="margin-top: 8px; color:#0f172a; font-weight: 700;">Vehicle: {{ $vehicleName }}</div>
                        <div style="margin-top: 4px; color:#0f172a; font-weight: 700;">Actual Duration: {{ $days > 0 ? ($days . ' Day(s)') : '—' }}</div>
                        <div style="margin-top: 4px; color:#0f172a; font-weight: 700;">Estimated Total: ₱{{ $amount }}</div>
                        <div style="margin-top: 8px; color:#64748b; font-weight: 800; font-size: 0.92rem;">
                            Any additional charges or refunds (if applicable) will be communicated by the owner.
                        </div>
                    </div>

                    <div style="background:#ffffff; border: 1px dashed rgba(245,158,11,0.55); border-radius: 12px; padding: 14px;">
                        <div style="font-weight: 900; color:#0f172a;">Feedback</div>
                        <div style="margin-top: 8px; color:#0f172a; font-weight: 700;">
                            We’d love to hear about your experience. You can leave a review from your bookings page.
                        </div>
                    </div>
                </div>

                <div style="margin-top: 14px; display:flex; gap: 10px; flex-wrap: wrap;">
                    <a href="{{ url('/my-bookings?status=Completed') }}" style="display:inline-block; background:#f59e0b; color:#0f172a; text-decoration:none; font-weight: 900; padding: 12px 16px; border-radius: 10px; border: 1px solid rgba(245,158,11,0.35);">
                        View Completed Booking
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

