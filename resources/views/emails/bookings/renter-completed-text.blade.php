@php
    $vehicle = $rental->vehicle;
    $ref = $rental->bookingReference();
    $from = $rental->datetime_from ? $rental->datetime_from->format('M d, Y - h:i A') : '—';
    $to = $rental->datetime_to ? $rental->datetime_to->format('M d, Y - h:i A') : '—';
    $hours = $rental->datetime_from && $rental->datetime_to ? $rental->datetime_from->diffInHours($rental->datetime_to) : 0;
    $days = $hours > 0 ? max(1, (int) floor($hours / 24)) : 0;
@endphp

AUTO AMEGOS RENT-A-CAR

Travel completed

Booking Reference: {{ $ref }}
Rental Period: {{ $from }} -> {{ $to }}

Final Summary
- Vehicle: {{ $vehicle?->name ?? '—' }}
- Actual Duration: {{ $days > 0 ? ($days . ' Day(s)') : '—' }}
- Estimated Total: ₱{{ number_format((float) ($rental->estimated_price ?? 0), 2) }}

Any additional charges or refunds (if applicable) will be communicated by the owner.

Feedback
Leave a review from your bookings page:
{{ url('/my-bookings?status=Completed') }}

Regards,
AARAAC Team

