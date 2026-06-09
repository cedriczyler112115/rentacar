@php
    $vehicle = $rental->vehicle;
    $renter = $rental->user;
    $ref = $rental->bookingReference();
    $from = $rental->datetime_from ? $rental->datetime_from->format('M d, Y - h:i A') : '—';
    $to = $rental->datetime_to ? $rental->datetime_to->format('M d, Y - h:i A') : '—';
@endphp

AUTO AMEGOS RENT-A-CAR

New booking request received

Booking Reference: {{ $ref }}
Rental Period: {{ $from }} -> {{ $to }}

Renter Details
- Name: {{ $renter?->name ?? '—' }}
- Email: {{ $renter?->email ?? '—' }}
- Address: {{ $renter?->address ?? '—' }}

Vehicle Details
- Vehicle: {{ $vehicle?->name ?? '—' }}
- Color: {{ $vehicle?->color ?? '—' }}
- Year Model: {{ $vehicle?->year_model ?? '—' }}
- License Plate: {{ $vehicle?->license_plate ?? '—' }}

Booking Details
- Pickup Location: {{ $rental->pickup_location ?? '—' }}
- Destination: {{ $rental->municipality ?? '—' }}, {{ $rental->province ?? '—' }}
- Estimated Amount: ₱{{ number_format((float) ($rental->estimated_price ?? 0), 2) }}

@if($rental->additional_message)
Special Instructions:
{{ $rental->additional_message }}
@endif

Review Booking: {{ url('/client-bookings') }}

Regards,
AARAAC Team
