@php
    $vehicle = $rental->vehicle;
    $ref = $rental->bookingReference();
    $from = $rental->datetime_from ? $rental->datetime_from->format('M d, Y - h:i A') : '—';
    $to = $rental->datetime_to ? $rental->datetime_to->format('M d, Y - h:i A') : '—';
@endphp

AUTO AMEGOS RENT-A-CAR

Vehicle dispatched

Dispatch Reference: {{ $ref }}
Travel Schedule: {{ $from }} -> {{ $to }}

Vehicle Details
- Vehicle: {{ $vehicle?->name ?? '—' }}
- Color: {{ $vehicle?->color ?? '—' }}
- Year Model: {{ $vehicle?->year_model ?? '—' }}
- License Plate: {{ $vehicle?->license_plate ?? '—' }}

Travel Details
- Pickup Location: {{ $rental->pickup_location ?? '—' }}
- Destination: {{ $rental->municipality ?? '—' }}, {{ $rental->province ?? '—' }}
- Estimated Amount: ₱{{ number_format((float) ($rental->estimated_price ?? 0), 2) }}

@if($rental->additional_message)
Notes:
{{ $rental->additional_message }}
@endif

Dispatched By
- {{ $dispatchedByName }}
- {{ $dispatchedByEmail }}

Open Dispatching: {{ url('/admin/dispatching') }}

Regards,
AARAAC Team

