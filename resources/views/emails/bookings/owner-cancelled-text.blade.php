@php
    $vehicle = $rental->vehicle;
    $renter = $rental->user;
    $ref = $rental->bookingReference();
    $from = $rental->datetime_from ? $rental->datetime_from->format('M d, Y - h:i A') : '—';
    $to = $rental->datetime_to ? $rental->datetime_to->format('M d, Y - h:i A') : '—';
@endphp

AUTO AMEGOS RENT-A-CAR

Booking cancelled by renter

Booking Reference: {{ $ref }}
Vehicle: {{ $vehicle?->name ?? '—' }}
Color: {{ $vehicle?->color ?? '—' }}
Year Model: {{ $vehicle?->year_model ?? '—' }}
Pickup Location: {{ $rental->pickup_location ?? '—' }}
Destination: {{ $rental->municipality ?? '—' }}, {{ $rental->province ?? '—' }}
Estimated Amount: ₱{{ number_format((float) ($rental->estimated_price ?? 0), 2) }}
Rental Period: {{ $from }} -> {{ $to }}
Renter: {{ $renter?->name ?? '—' }} ({{ $renter?->email ?? '—' }})

@if($reason)
Reason:
{{ $reason }}
@endif

View Bookings: {{ url('/client-bookings') }}

Regards,
AARAAC Team
