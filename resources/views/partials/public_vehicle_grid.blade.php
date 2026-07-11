<div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 18px;">
    <div style="font-weight: 800; color: #0f172a;">
        @if($vehicles->total() > 0)
            Showing {{ $vehicles->firstItem() }}-{{ $vehicles->lastItem() }} of {{ $vehicles->total() }} available vehicles
        @else
            No available vehicles found
        @endif
    </div>
    @if($vehicles->hasPages())
        <div style="font-size: 0.9rem; color: #64748b; font-weight: 700;">
            Page {{ $vehicles->currentPage() }} of {{ $vehicles->lastPage() }}
        </div>
    @endif
</div>

<div class="vehicle-grid">
    @forelse($vehicles as $vehicle)
        <div class="vehicle-card">
            <div class="card-img-wrapper"
                onclick="viewImages({{ $vehicle->id }}, {{ $vehicle->images->sortByDesc('is_primary')->map(function($img) { return ['url' => Storage::url($img->image_path), 'is_primary' => (bool)$img->is_primary]; })->values()->toJson() }})"
                title="Click to view images">

                @php
                    $primaryImage = $vehicle->images->where('is_primary', true)->first() ?? $vehicle->images->first();
                @endphp

                @if($primaryImage)
                    <img src="{{ Storage::url($primaryImage->image_path) }}" alt="{{ $vehicle->name }}">
                @else
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5">
                        <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/>
                        <circle cx="7" cy="17" r="2"/>
                        <circle cx="17" cy="17" r="2"/>
                    </svg>
                @endif

                {{-- ✅ FIXED STATUS BADGE --}}
                @php
                    $statusName = strtolower($vehicle->libAvailabilityStatus->name ?? '');
                    if ($statusName == 'available') {
                        $statusColor = '#10b981';
                    } elseif ($statusName == 'pending') {
                        $statusColor = '#3b82f6';
                    } elseif ($statusName == 'rented') {
                        $statusColor = '#ef4444';
                    } else {
                        $statusColor = '#f59e0b';
                    }
                @endphp

                <span class="status-badge" style="background: {{ $statusColor }};">
                    {{ $vehicle->libAvailabilityStatus->name ?? 'Unknown' }}
                </span>
            </div>

            <div style="padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 800; color: var(--primary); margin: 0 0 4px 0;">
                            {{ $vehicle->name }} {{ $vehicle->year_model ? '('.$vehicle->year_model.')' : '' }}
                        </h3>
                        <p style="color: #64748b; font-size: 0.95rem; font-weight: 600; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">
                            {{ $vehicle->libBrand->name ?? 'Unknown' }}
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: .75rem; font-weight: 400; color: var(--accent);">Starts at</span>
                        <span style="font-size: 1.25rem; font-weight: 800; color: var(--accent);">
                            ₱{{ number_format($vehicle->price_per_day, 2) }}
                        </span>
                        <div style="font-size: 0.8rem; color: #64748b;">per day</div>
                    </div>
                </div>

                @php
                    $availabilityText = 'Available anytime';
                    $availabilityTooltip = '';
                    $ad = $vehicle->booked_dates;

                    if (is_array($ad) && count($ad) > 0) {
                        $availabilityText = 'Booked dates';
                        $parts = [];

                        foreach ($ad as $item) {
                            if (is_string($item)) {
                                try {
                                    $parts[] = \Carbon\Carbon::createFromFormat('Y-m-d', $item)->format('F j, Y');
                                } catch (\Throwable $e) {}
                                continue;
                            }

                            if (is_array($item) && isset($item['start'], $item['end'])) {
                                try {
                                    $start = \Carbon\Carbon::createFromFormat('Y-m-d', $item['start']);
                                    $end = \Carbon\Carbon::createFromFormat('Y-m-d', $item['end']);

                                    if ($start->year === $end->year && $start->month === $end->month) {
                                        $parts[] = $start->format('F j') . '-' . $end->format('j, Y');
                                    } elseif ($start->year === $end->year) {
                                        $parts[] = $start->format('F j') . '-' . $end->format('F j, Y');
                                    } else {
                                        $parts[] = $start->format('F j, Y') . '-' . $end->format('F j, Y');
                                    }
                                } catch (\Throwable $e) {}
                            }
                        }

                        $availabilityTooltip = implode("\n", $parts);
                    }
                @endphp

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px;">
                    <div>{{ $vehicle->libType->name ?? 'N/A' }}</div>
                    <div>{{ $vehicle->libTransmission->name ?? 'N/A' }}</div>
                    <div>{{ $vehicle->libFuelType->name ?? 'N/A' }}</div>
                    <div>{{ Str::limit($vehicle->color ?? 'Any Color', 12) }}</div>
                    <div>{{ $vehicle->displacement ?? 'N/A' }}</div>
                    <div>{{ $vehicle->seating_capacity }} Seats</div>

                    <div>
                        @if($availabilityTooltip)
                            <span data-tooltip="{{ $availabilityTooltip }}">{{ $availabilityText }}</span>
                        @else
                            <span>{{ $availabilityText }}</span>
                        @endif
                    </div>

                    <div>{{ $vehicle->images->count() }} Images</div>
                </div>

                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('book.now', $vehicle) }}" class="btn btn-primary">
                        Book Now!
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div style="text-align:center; padding:50px;">
            <h3>No vehicles match your search</h3>
        </div>
    @endforelse
</div>

@if($vehicles->hasPages())
    <div class="fleet-pagination" style="margin-top: 24px;">
        {{ $vehicles->links() }}
    </div>
@endif
