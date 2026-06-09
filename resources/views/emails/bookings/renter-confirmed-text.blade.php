@php
    $vehicle = $rental->vehicle;
    $ref = $rental->bookingReference();
    $from = $rental->datetime_from ? $rental->datetime_from->format('M d, Y - h:i A') : '—';
    $to = $rental->datetime_to ? $rental->datetime_to->format('M d, Y - h:i A') : '—';
@endphp

AUTO AMEGOS RENT-A-CAR

Booking confirmed

Booking Reference: {{ $ref }}
Rental Period: {{ $from }} -> {{ $to }}

Vehicle Details
- Vehicle: {{ $vehicle?->name ?? '—' }}
- Color: {{ $vehicle?->color ?? '—' }}
- Year Model: {{ $vehicle?->year_model ?? '—' }}
- License Plate: {{ $vehicle?->license_plate ?? '—' }}

Pickup & Destination
- Pickup Location: {{ $rental->pickup_location ?? '—' }}
- Destination: {{ $rental->municipality ?? '—' }}, {{ $rental->province ?? '—' }}

Cost Breakdown (Estimate)
- Destination Charge: ₱{{ number_format((float) ($rental->destination_price ?? 0), 2) }}
- Carwash Fee: ₱{{ number_format((float) ($rental->carwash_fee ?? 0), 2) }}
- Extra Hours Fee: ₱{{ number_format((float) ($rental->extra_hours_fee ?? 0), 2) }}
- Estimated Total: ₱{{ number_format((float) ($rental->estimated_price ?? 0), 2) }}

Next Steps
- Arrive on time with a valid government-issued ID.
- Bring your driver’s license (valid and readable).
- Prepare any payment proof if required by the owner.

View My Booking: {{ url('/my-bookings') }}

Regards,
AARAAC Team

