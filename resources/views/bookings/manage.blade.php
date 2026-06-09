<x-member-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2>{{ __('Manage Client Bookings') }}</h2>
                <p style="color: #64748b; margin-top: 5px;">View bookings made by other users for the vehicles you own.</p>
            </div>
            <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
                <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></span>
                {{ __("You're logged in!") }}
            </div>
        </div>
    </x-slot>

    <div class="container" style="padding: 0px 20px 40px 20px;" x-data="imageCarousel()">
        <div class="toolbar">
            <div class="filter-container">
                <a href="{{ route('bookings.manage', request()->except('page', 'status')) }}" class="filter-btn {{ !request('status') ? 'active' : '' }}">
                    All Bookings <span class="filter-count">{{ $allCount }}</span>
                </a>
                <a href="{{ route('bookings.manage', array_merge(request()->except('page', 'status'), ['status' => 'Pending'])) }}" class="filter-btn {{ request('status') == 'Pending' ? 'active' : '' }}">
                    Pending <span class="filter-count">{{ $statusCounts['Pending'] ?? 0 }}</span>
                </a>
                <a href="{{ route('bookings.manage', array_merge(request()->except('page', 'status'), ['status' => 'Confirmed'])) }}" class="filter-btn {{ request('status') == 'Confirmed' ? 'active' : '' }}">
                    Confirmed <span class="filter-count">{{ $statusCounts['Confirmed'] ?? 0 }}</span>
                </a>
                <a href="{{ route('bookings.manage', array_merge(request()->except('page', 'status'), ['status' => 'Completed'])) }}" class="filter-btn {{ request('status') == 'Completed' ? 'active' : '' }}">
                    Completed <span class="filter-count">{{ $statusCounts['Completed'] ?? 0 }}</span>
                </a>
                <a href="{{ route('bookings.manage', array_merge(request()->except('page', 'status'), ['status' => 'Cancelled'])) }}" class="filter-btn {{ request('status') == 'Cancelled' ? 'active' : '' }}">
                    Cancelled by Client <span class="filter-count">{{ $statusCounts['Cancelled'] ?? 0 }}</span>
                </a>
                <a href="{{ route('bookings.manage', array_merge(request()->except('page', 'status'), ['status' => 'Rejected'])) }}" class="filter-btn {{ request('status') == 'Rejected' ? 'active' : '' }}">
                    Rejected <span class="filter-count">{{ $statusCounts['Rejected'] ?? 0 }}</span>
                </a>
            </div>

            <!-- Right-side controls removed -->
        </div>

        @if($bookings->count() > 0)
            <div style="display: grid; grid-template-columns: 1fr; gap: 30px;">
                @foreach($bookings as $booking)
                    <div class="booking-card {{ $booking->status === 'Pending' ? 'popover-pending' : '' }}">
                        <div class="booking-header">
                            <div class="header-main">
                                <span class="booking-ref">Reference: #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
                                <span class="status-badge status-{{ strtolower($booking->status) }}">{{ $booking->status }}</span>
                                @php
                                    $rawReferralValue = $booking->referral ?? null;
                                    $referralValue = $rawReferralValue === 'Internal Referral'
                                        ? 'AARACC Booking'
                                        : ($rawReferralValue === 'External Referral' ? 'Online Booking' : $rawReferralValue);
                                    $referralClass = $referralValue === 'AARACC Booking'
                                        ? 'referral-internal'
                                        : ($referralValue === 'Online Booking' ? 'referral-external' : 'referral-other');
                                @endphp
                                @if(!empty($referralValue))
                                    <span class="referral-badge {{ $referralClass }}">{{ $referralValue }}</span>
                                @endif
                            </div>
                            <div class="booking-timestamp">Booked on {{ $booking->created_at->format('F j, Y g:i A') }}</div>
                        </div>

                        <div class="booking-content">
                            <div class="vehicle-visual">
                                @php
                                    $primaryImage = $booking->vehicle->images->where('is_primary', true)->first() ?? $booking->vehicle->images->first();
                                    $allImages = $booking->vehicle->images->map(fn($img) => Storage::url($img->image_path))->toArray();
                                @endphp
                                <div class="main-image-wrapper" @click="openCarousel({{ json_encode($allImages) }})">
                                    @if($primaryImage)
                                        <img src="{{ Storage::url($primaryImage->image_path) }}" alt="{{ $booking->vehicle->name }}">
                                    @else
                                        <div class="placeholder-img">
                                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M7 10v4m4-4v4m4-4v4M5 21h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2z"/></svg>
                                            <span>No Image Available</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="quick-info">
                                    <div class="info-item">
                                        @php
                                            $rawReferralLabelValue = $booking->referral ?? '';
                                            $normalizedReferralLabelValue = $rawReferralLabelValue === 'Internal Referral'
                                                ? 'AARACC Booking'
                                                : ($rawReferralLabelValue === 'External Referral' ? 'Online Booking' : $rawReferralLabelValue);
                                        @endphp
                                        <span class="label">{{ $normalizedReferralLabelValue === 'AARACC Booking' ? $normalizedReferralLabelValue : 'Renter' }}</span>
                                        @if($booking->status === 'Cancelled')
                                            <span class="value" style="display:inline-flex; align-items:center; gap:8px;">
                                                @if($booking->user && $booking->user->profile_photo_path)
                                                    <img src="{{ Storage::url($booking->user->profile_photo_path) }}" alt="Renter Photo" style="width: 22px; height: 22px; border-radius: 999px; object-fit: cover; border: 2px solid rgba(245, 158, 11, 0.5);">
                                                @else
                                                    <span style="width: 22px; height: 22px; border-radius: 999px; background: rgba(245, 158, 11, 0.15); border: 2px solid rgba(245, 158, 11, 0.5); display:inline-flex; align-items:center; justify-content:center; font-weight: 900; color: var(--accent); font-size: 0.75rem;">
                                                        {{ strtoupper(substr($booking->user->name ?? 'U', 0, 1)) }}
                                                    </span>
                                                @endif
                                                {{ $booking->user->name ?? 'N/A' }}
                                            </span>
                                            <span class="subvalue" style="display:inline-flex; align-items:center; gap:8px;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z" fill="none"/><path d="M22 6l-10 7L2 6"/></svg>
                                                {{ $booking->user->email ?? '' }}
                                            </span>
                                        @elseif(in_array($booking->status, ['Confirmed', 'Completed'], true))
                                            <span class="value" style="display:inline-flex; align-items:center; gap:8px;">
                                                @if($booking->user && $booking->user->profile_photo_path)
                                                    <img src="{{ Storage::url($booking->user->profile_photo_path) }}" alt="Renter Photo" style="width: 22px; height: 22px; border-radius: 999px; object-fit: cover; border: 2px solid rgba(245, 158, 11, 0.5);">
                                                @else
                                                    <span style="width: 22px; height: 22px; border-radius: 999px; background: rgba(245, 158, 11, 0.15); border: 2px solid rgba(245, 158, 11, 0.5); display:inline-flex; align-items:center; justify-content:center; font-weight: 900; color: var(--accent); font-size: 0.75rem;">
                                                        {{ strtoupper(substr($booking->user->name ?? 'U', 0, 1)) }}
                                                    </span>
                                                @endif
                                                {{ $booking->user->name ?? 'N/A' }}
                                            </span>
                                            <span class="subvalue" style="display:inline-flex; align-items:center; gap:8px;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z" fill="none"/><path d="M22 6l-10 7L2 6"/></svg>
                                                {{ $booking->user->email ?? '' }}
                                            </span>
                                            <span class="subvalue contact-row">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.86 19.86 0 0 1 2.08 4.18 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72c.12.86.31 1.7.57 2.5a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.58-1.09a2 2 0 0 1 2.11-.45c.8.26 1.64.45 2.5.57A2 2 0 0 1 22 16.92z"/></svg>
                                                {{ $booking->user->contact_number ?: 'Not yet confirmed' }}
                                            </span>
                                            <span class="subvalue address-row">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                                {{ $booking->user->address ?: 'Not yet confirmed' }}
                                            </span>
                                        @else
                                            <span class="value" style="display:inline-flex; align-items:center; gap:8px;">
                                                @if($booking->user && $booking->user->profile_photo_path)
                                                    <img src="{{ Storage::url($booking->user->profile_photo_path) }}" alt="Renter Photo" style="width: 22px; height: 22px; border-radius: 999px; object-fit: cover; border: 2px solid rgba(245, 158, 11, 0.5);">
                                                @else
                                                    <span style="width: 22px; height: 22px; border-radius: 999px; background: rgba(245, 158, 11, 0.15); border: 2px solid rgba(245, 158, 11, 0.5); display:inline-flex; align-items:center; justify-content:center; font-weight: 900; color: var(--accent); font-size: 0.75rem;">
                                                        {{ strtoupper(substr($booking->user->name ?? 'U', 0, 1)) }}
                                                    </span>
                                                @endif
                                                {{ $booking->user->name ?? 'N/A' }}
                                            </span>
                                            <span class="subvalue" style="display:inline-flex; align-items:center; gap:8px;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z" fill="none"/><path d="M22 6l-10 7L2 6"/></svg>
                                                {{ $booking->user->email ?? '' }}
                                            </span>
                                            <span class="subvalue contact-row">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.86 19.86 0 0 1 2.08 4.18 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72c.12.86.31 1.7.57 2.5a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.58-1.09a2 2 0 0 1 2.11-.45c.8.26 1.64.45 2.5.57A2 2 0 0 1 22 16.92z"/></svg>
                                                Not yet confirmed
                                            </span>
                                            <span class="subvalue address-row">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                                Not yet confirmed
                                            </span>
                                        @endif
                                    </div>
                                    <div class="info-item">
                                        <span class="label">Estimated Service Fee</span>
                                        <span class="value accent">₱{{ number_format($booking->estimated_price, 2) }}</span>
                                    </div>
                                    @if(!empty($booking->additional_message))
                                    <div class="info-item">
                                        <span class="label">Message to Owner / Offers</span>
                                        <span class="value" style="white-space: pre-wrap; color: #0f172a; font-weight:700;">{{ $booking->additional_message }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="vehicle-specs">
                                <div class="specs-header" role="group" aria-label="Vehicle details and actions">
                                    <div class="specs-left">
                                        <h3 class="vehicle-name">{{ $booking->vehicle->name }}</h3>
                                        <span class="brand-tag">{{ $booking->vehicle->libBrand->name ?? 'N/A' }}</span>
                                    </div>
                                    <div style="display:flex; gap:8px; align-items:center;">
                                        @if($booking->status === 'Pending')
                                            <button type="button" class="cancel-btn" data-rental-id="{{ $booking->id }}" aria-label="Reject Booking">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                                Reject
                                            </button>
                                            <button type="button" class="approve-btn" data-rental-id="{{ $booking->id }}" aria-label="Confirm">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                                                Confirm
                                            </button>
                                        @elseif($booking->status === 'Confirmed')
                                            <button type="button" class="approve-btn complete-btn" data-rental-id="{{ $booking->id }}" aria-label="Travel Completed">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                                                Travel Completed
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <div class="specs-grid">
                                    <div class="spec-item">
                                        <div class="spec-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        </div>
                                        <div class="spec-text">
                                            <span class="spec-label">Year Model</span>
                                            <span class="spec-value">{{ $booking->vehicle->year_model ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="spec-item">
                                        <div class="spec-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 3h12"/><path d="M12 3v7"/><path d="M8 10h8"/><path d="M6 10v9a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-9"/></svg>
                                        </div>
                                        <div class="spec-text">
                                            <span class="spec-label">Displacement</span>
                                            <span class="spec-value uppercase">{{ $booking->vehicle->displacement ?? 'N/A' }} cc</span>
                                        </div>
                                    </div>
                                    <div class="spec-item">
                                        <div class="spec-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                        </div>
                                        <div class="spec-text">
                                            <span class="spec-label">Seating</span>
                                            <span class="spec-value">{{ $booking->vehicle->seating_capacity }} Seats</span>
                                        </div>
                                    </div>
                                    <div class="spec-item">
                                        <div class="spec-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="14" rx="2"/><path d="M7 8h10"/><path d="M7 12h6"/></svg>
                                        </div>
                                        <div class="spec-text">
                                            <span class="spec-label">Driver's License</span>
                                            <span class="spec-value">
                                                @if($booking->drivers_license_path)
                                                    <span class="doc-links">
                                                        <button type="button" class="doc-link" data-url="{{ Storage::url($booking->drivers_license_path) }}" data-title="Driver's License">View Driver's License</button>
                                                    </span>
                                                @else
                                                    None
                                                @endif
                                            </span>
                                        </div>
                                    </div>                                     
                                    <div class="spec-item">
                                        <div class="spec-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h13v14H3z"/><path d="M16 8h5l-1.5 2.5L21 13h-5z"/><path d="M7 18h5"/><path d="M9.5 18v3"/></svg>
                                        </div>
                                        <div class="spec-text">
                                            <span class="spec-label">Fuel Type</span>
                                            <span class="spec-value">{{ $booking->vehicle->libFuelType->name ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="spec-item">
                                        <div class="spec-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 10h10"/><path d="M9 6h6"/><path d="M9 14h6"/><path d="M12 3v18"/></svg>
                                        </div>
                                        <div class="spec-text">
                                            <span class="spec-label">Transmission</span>
                                            <span class="spec-value">{{ $booking->vehicle->libTransmission->name ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="spec-item">
                                        <div class="spec-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 13l2-5h14l2 5"/><path d="M5 13v6h2v-2h10v2h2v-6"/><circle cx="8" cy="13" r="1"/><circle cx="16" cy="13" r="1"/></svg>
                                        </div>
                                        <div class="spec-text">
                                            <span class="spec-label">Type</span>
                                            <span class="spec-value">{{ $booking->vehicle->libType->name ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="spec-item">
                                        <div class="spec-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                                        </div>
                                        <div class="spec-text">
                                            <span class="spec-label">Downpayment Attachments</span>
                                            <span class="spec-value">
                                                @if(!empty($booking->downpayment_attachments))
                                                    <span class="doc-links">
                                                        @foreach($booking->downpayment_attachments as $i => $p)
                                                            <button type="button" class="doc-link" data-url="{{ Storage::url($p) }}" data-title="Downpayment Attachment {{ $i + 1 }}">View Downpayment</button>
                                                        @endforeach
                                                    </span>
                                                @else
                                                    None
                                                @endif
                                            </span>
                                        </div>
                                    </div>                                      
                                </div>

                                <div class="details-grid">
                                    <div class="computation-box">
                                        <div class="details-header">Estimated Service Fee Computation</div>
                                        <div class="computation-grid">
                                            <div class="computation-item">
                                                <span class="c-label">Destination Charge <br>({{ $booking->municipality }}, {{ $booking->province }})</span>
                                                <span class="c-value">₱{{ number_format($booking->destination_price, 2) }}</span>
                                            </div>
                                            @php
                                                $from = \Carbon\Carbon::parse($booking->datetime_from);
                                                $to = \Carbon\Carbon::parse($booking->datetime_to);
                                                $totalHours = $from->diffInHours($to);
                                                $days = floor($totalHours / 24);
                                                if ($days == 0 && (int)($booking->extra_hours ?? 0) === 0) $days = 1;
                                                $dailyRate = (float) ($booking->vehicle->price_per_day ?? 0);
                                                $rentalAmount = $days * $dailyRate;
                                            @endphp
                                            <div class="computation-item">
                                                <span class="c-label">Number of Days</span>
                                                <span class="c-value">{{ $days }} Day(s)</span>
                                            </div>
                                            <div class="computation-item">
                                                <span class="c-label">Rental Amount ({{ $days }} × ₱{{ number_format($dailyRate, 2) }}/day)</span>
                                                <span class="c-value">₱{{ number_format($rentalAmount, 2) }}</span>
                                            </div>
                                            <div class="computation-item">
                                                <span class="c-label">Carwash Fee</span>
                                                <span class="c-value">₱{{ number_format($booking->carwash_fee ?? 0, 2) }}</span>
                                            </div>
                                            <div class="computation-item">
                                                <span class="c-label">Extra Hours Fee ({{ $booking->extra_hours }} hrs)</span>
                                                <span class="c-value">₱{{ number_format($booking->extra_hours_fee, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="computation-total">
                                            <span class="total-label">Total Estimated Service Fee</span>
                                            <span class="total-value">₱{{ number_format($booking->estimated_price, 2) }}</span>
                                        </div>
                                    </div>

                                    <div class="itinerary-box">
                                        <div class="details-header">Travel Itinerary</div>
                                        <div class="itinerary-grid">
                                            <div class="itinerary-item">
                                                <span class="dot"></span>
                                                <div class="itinerary-info">
                                                    <span class="i-label">Pickup Date & Location</span>
                                                    <span class="i-value">{{ \Carbon\Carbon::parse($booking->datetime_from)->format('M d, Y - h:i A') }} <br> {{ $booking->pickup_location ?? 'Main Branch, Butuan City' }}</span>
                                                </div>
                                            </div>
                                            <div class="itinerary-item">
                                                <span class="dot accent"></span>
                                                <div class="itinerary-info">
                                                    <span class="i-label">Return Date</span>
                                                    <span class="i-value">{{ \Carbon\Carbon::parse($booking->datetime_to)->format('M d, Y - h:i A') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="logs-box">
                                        <div class="details-header">Logs</div>
                                        <div class="logs-box-body">
                                            @if($booking->logs && $booking->logs->count() > 0)
                                                @foreach($booking->logs->sortBy('created_at') as $log)
                                                    <div class="log-row">
                                                        <div class="log-meta">{{ $log->created_at->format('M d, Y h:i A') }}</div>
                                                        <div class="log-text">
                                                            {{ strtoupper($log->action) }}
                                                            {{ $log->user->id === auth()->id() ? '(You)' : '(' . ($log->user->name ?? 'User') . ')' }}
                                                            @if(($log->previous_values['status'] ?? null) && ($log->new_values['status'] ?? null))
                                                                - {{ $log->previous_values['status'] }} → {{ $log->new_values['status'] }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="log-empty">No logs yet</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 40px;">
                {{ $bookings->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 17h6"/><path d="M10 13h4"/><path d="M12 2v2"/><path d="M12 8v2"/><path d="M12 14v2"/><path d="M12 20v2"/><rect x="5" y="2" width="14" height="20" rx="2"/></svg>
                </div>
                <h3>No Bookings Found</h3>
                <p>No other users have bookings for your vehicles with the selected filters.</p>
            </div>
        @endif

        <div x-show="isOpen" @keydown.escape.window="isOpen = false" class="carousel-modal">
            <div class="carousel-backdrop" @click="isOpen = false"></div>
            <button class="carousel-close-btn" @click="isOpen = false">&times;</button>
            <div class="carousel-content">
                <div class="main-image-container">
                    <img :src="images[activeIndex]" alt="Vehicle Image">
                    <button class="carousel-nav prev" @click="prev">&lsaquo;</button>
                    <button class="carousel-nav next" @click="next">&rsaquo;</button>
                </div>
                <div class="thumbnail-container">
                    <template x-for="(image, index) in images">
                        <div class="thumbnail" :class="{'active': index === activeIndex}" @click="activeIndex = index">
                            <img :src="image" alt="Thumbnail">
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div id="docModal" class="doc-modal" aria-hidden="true">
            <div class="doc-backdrop"></div>
            <div class="doc-panel" role="dialog" aria-modal="true" aria-labelledby="docModalTitle">
                <div class="doc-header">
                    <div id="docModalTitle" class="doc-title"></div>
                    <button type="button" class="doc-close" aria-label="Close document viewer">&times;</button>
                </div>
                <div class="doc-body">
                    <img id="docImage" class="doc-image" alt="" style="display:none;">
                    <iframe id="docFrame" class="doc-frame" style="display:none;" title="Document preview"></iframe>
                </div>
            </div>
        </div>
    </div>

    <style>
        .toolbar { margin-bottom: 30px; display: flex; justify-content: flex-start; align-items: center; gap: 16px; flex-wrap: wrap; }

        .filter-container { display: flex; gap: 10px; background: white; padding: 6px; border-radius: 40px; box-shadow: var(--shadow-sm); border: 1px solid #e2e8f0; overflow-x: auto; -ms-overflow-style: none; scrollbar-width: none; }
        .filter-container::-webkit-scrollbar { display: none; }
        .filter-btn { padding: 10px 24px; border-radius: 30px; font-weight: 600; color: #64748b; transition: var(--transition); white-space: nowrap; font-size: 0.95rem; }
        .filter-btn.active { background: var(--primary); color: white; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2); }
        .filter-btn:not(.active):hover { background: #f1f5f9; color: var(--primary); }
        .filter-count { background: #e2e8f0; color: #475569; padding: 2px 8px; border-radius: 10px; font-size: 0.8rem; font-weight: 700; margin-left: 8px; }
        .filter-btn.active .filter-count { background: var(--accent); color: var(--primary); }

        .booking-card { background: white; border-radius: 16px; box-shadow: var(--shadow-sm); border: 1px solid #e2e8f0; overflow: hidden; transition: var(--transition); }
        .booking-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
        .booking-card { position: relative; }
        .booking-card::after {
            content: "Contact number and other renter details will display once confirmed.";
            position: absolute;
            top: 12px;
            right: 12px;
            max-width: 320px;
            background: rgba(2, 6, 23, 0.92);
            color: white;
            padding: 10px 12px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.8rem;
            opacity: 0;
            transform: translateY(6px);
            pointer-events: none;
            transition: opacity 0.18s ease, transform 0.18s ease;
            z-index: 5;
        }
        .booking-card.popover-pending:hover::after { opacity: 1; transform: translateY(0); }
        .booking-header { padding: 16px 24px; background: #f8fafc; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; }
        .header-main { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .booking-ref { font-weight: 800; color: var(--primary); letter-spacing: 0.5px; font-size: 1.05rem; }
        .booking-timestamp { font-size: 0.85rem; color: #94a3b8; font-weight: 500; }

        .status-badge { padding: 5px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-pending { background: #fffbeb; color: #d97706; border: 1px solid #fef3c7; }
        .status-confirmed { background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; }
        .status-completed { background: #f5f3ff; color: #7c3aed; border: 1px solid #ede9fe; }
        .status-cancelled { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; }
        .status-rejected { background: #fff1f2; color: #be123c; border: 1px solid #ffe4e6; }
        .referral-badge { padding: 5px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
        .referral-internal { background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
        .referral-external { background: #ecfeff; color: #0e7490; border: 1px solid #cffafe; }
        .referral-other { background: #f8fafc; color: #334155; border: 1px solid #e2e8f0; }

        .booking-content { display: grid; grid-template-columns: 320px 1fr; gap: 32px; padding: 24px; }
        .vehicle-visual { display: flex; flex-direction: column; gap: 20px; }
        .main-image-wrapper { width: 100%; height: 200px; border-radius: 12px; overflow: hidden; background: #f1f5f9; border: 1px solid #e2e8f0; cursor: pointer; }
        .main-image-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: var(--transition); }
        .booking-card:hover .main-image-wrapper img { transform: scale(1.05); }
        .placeholder-img { height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; gap: 10px; font-size: 0.85rem; }

        .quick-info { display: grid; grid-template-columns: 1fr; gap: 12px; }
        .info-item { background: #f8fafc; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 4px; }
        .info-item .label { font-size: 0.7rem; text-transform: uppercase; font-weight: 700; color: #94a3b8; letter-spacing: 0.5px; }
        .info-item .value { font-weight: 800; color: var(--primary); font-size: 0.95rem; }
        .info-item .subvalue { font-weight: 600; color: #64748b; font-size: 0.85rem; }
        .contact-row, .address-row { display: inline-flex; align-items: center; gap: 8px; }
        .contact-row svg, .address-row svg { color: var(--accent); flex-shrink: 0; }
        .info-item .value.accent { color: var(--accent); }

        .vehicle-specs { display: flex; flex-direction: column; gap: 24px; }
        .specs-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; }
        .specs-left { display: flex; align-items: center; gap: 12px; }
        .vehicle-name { font-size: 1.6rem; font-weight: 800; color: var(--primary); margin: 0; line-height: 1.2; }
        .brand-tag { background: var(--accent); color: white; padding: 4px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .approve-btn { display: inline-flex; align-items: center; gap: 8px; background: var(--accent); color: #1f2937; padding: 10px 16px; border: 1px solid var(--accent); border-radius: 10px; font-weight: 800; cursor: pointer; transition: var(--transition); }
        .approve-btn:hover { filter: brightness(1.05); transform: translateY(-1px); }
        .cancel-btn { display: inline-flex; align-items: center; gap: 8px; background: #fee2e2; color: #991b1b; padding: 10px 16px; border: 1px solid #fecaca; border-radius: 10px; font-weight: 800; cursor: pointer; transition: var(--transition); }
        .cancel-btn:hover { filter: brightness(1.02); transform: translateY(-1px); }
        .specs-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .spec-item { display: flex; align-items: center; gap: 12px; }
        .spec-icon { width: 36px; height: 36px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--primary); transition: var(--transition); }
        .booking-card:hover .spec-icon { background: var(--accent); color: white; }
        .spec-text { display: flex; flex-direction: column; }
        .spec-label { font-size: 0.7rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; }
        .spec-value { font-size: 0.9rem; font-weight: 700; color: var(--primary); }
        .doc-links { display: inline-flex; gap: 8px; flex-wrap: wrap; }
        .doc-link { background: white; border: 1px solid #e2e8f0; color: var(--primary); padding: 6px 10px; border-radius: 999px; font-weight: 800; font-size: 0.75rem; cursor: pointer; transition: var(--transition); }
        .doc-link:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-1px); }

        .details-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .computation-box, .itinerary-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; }
        .logs-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; }
        .details-header { font-size: 0.8rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px; }
        .computation-grid { display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px; }
        .computation-item { display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem; gap: 10px; }
        .c-label { color: #64748b; }
        .c-value { font-weight: 700; color: var(--primary); white-space: nowrap; }
        .computation-total { border-top: 2px solid #e2e8f0; padding-top: 12px; display: flex; justify-content: space-between; align-items: center; }
        .total-label { font-weight: 700; color: var(--primary); }
        .total-value { font-weight: 800; font-size: 1.2rem; color: var(--accent); }

        .itinerary-grid { display: flex; flex-direction: column; gap: 16px; }
        .itinerary-item { display: flex; gap: 12px; align-items: flex-start; }
        .dot { width: 10px; height: 10px; border-radius: 50%; background: #94a3b8; margin-top: 5px; flex-shrink: 0; }
        .dot.accent { background: var(--accent); box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1); }
        .itinerary-info { display: flex; flex-direction: column; gap: 2px; }
        .i-label { font-size: 0.65rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; }
        .i-value { font-size: 0.85rem; font-weight: 700; color: var(--primary); }

        .logs-box-body { display: flex; flex-direction: column; gap: 10px; max-height: 220px; overflow: auto; padding-right: 6px; }
        .log-row { display: flex; flex-direction: column; gap: 2px; padding: 10px; border-radius: 10px; border: 1px solid #e2e8f0; background: white; }
        .log-meta { font-size: 0.72rem; font-weight: 800; color: #94a3b8; }
        .log-text { font-size: 0.82rem; font-weight: 800; color: var(--primary); }
        .log-empty { font-size: 0.85rem; font-weight: 700; color: #64748b; }

        .empty-state { text-align: center; padding: 80px 40px; background: white; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .empty-icon { color: #cbd5e1; margin-bottom: 24px; }
        .empty-state h3 { font-size: 1.5rem; font-weight: 800; color: var(--primary); margin-bottom: 12px; }
        .empty-state p { color: #64748b; margin-bottom: 0; }

        .carousel-modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 2000; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .carousel-backdrop { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px); }
        .carousel-close-btn { position: absolute; top: 20px; right: 30px; font-size: 3rem; color: white; background: none; border: none; cursor: pointer; line-height: 1; opacity: 0.8; transition: var(--transition); }
        .carousel-close-btn:hover { opacity: 1; transform: rotate(90deg); }
        .carousel-content { position: relative; z-index: 1; width: 100%; max-width: 900px; display: flex; flex-direction: column; gap: 16px; }
        .main-image-container { position: relative; width: 100%; height: 500px; background: #0f172a; border-radius: 12px; overflow: hidden; border: 1px solid #1e293b; }
        .main-image-container img { width: 100%; height: 100%; object-fit: contain; }
        .carousel-nav { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255, 255, 255, 0.1); color: white; border: 1px solid rgba(255, 255, 255, 0.2); width: 44px; height: 44px; border-radius: 50%; font-size: 1.5rem; cursor: pointer; transition: var(--transition); }
        .carousel-nav:hover { background: var(--accent); border-color: var(--accent); }
        .carousel-nav.prev { left: 16px; }
        .carousel-nav.next { right: 16px; }
        .thumbnail-container { display: flex; gap: 12px; justify-content: center; overflow-x: auto; padding: 10px; }
        .thumbnail { width: 80px; height: 60px; border-radius: 6px; overflow: hidden; cursor: pointer; border: 2px solid transparent; transition: var(--transition); }
        .thumbnail img { width: 100%; height: 100%; object-fit: cover; }
        .thumbnail.active { border-color: var(--accent); box-shadow: 0 0 15px rgba(245, 158, 11, 0.5); }
        .thumbnail:not(.active):hover { transform: scale(1.05); }

        .doc-modal { position: fixed; inset: 0; z-index: 2200; display: none; align-items: center; justify-content: center; padding: 20px; }
        .doc-modal.active { display: flex; }
        .doc-backdrop { position: absolute; inset: 0; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px); }
        .doc-panel { position: relative; z-index: 1; width: 100%; max-width: 900px; background: #0b1220; border: 1px solid #1e293b; border-radius: 12px; overflow: hidden; box-shadow: 0 18px 35px rgba(0,0,0,0.25); }
        .doc-header { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: rgba(2, 6, 23, 0.65); border-bottom: 1px solid #1e293b; }
        .doc-title { color: white; font-weight: 800; }
        .doc-close { background: none; border: none; color: white; font-size: 2rem; line-height: 1; cursor: pointer; opacity: 0.85; transition: var(--transition); }
        .doc-close:hover { opacity: 1; transform: rotate(90deg); }
        .doc-body { height: min(75vh, 720px); background: #0b1220; display: flex; align-items: center; justify-content: center; padding: 10px; }
        .doc-image { max-width: 100%; max-height: 100%; object-fit: contain; }
        .doc-frame { width: 100%; height: 100%; border: 0; background: #0b1220; }

        @media (max-width: 1024px) {
            .booking-content { grid-template-columns: 1fr; gap: 24px; }
            .specs-grid { grid-template-columns: 1fr 1fr; }
            .details-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .booking-header { flex-direction: column; align-items: flex-start; gap: 12px; }
            .specs-grid { grid-template-columns: 1fr; }
            .main-image-container { height: 300px; }
            .thumbnail { width: 60px; height: 45px; }
        }
    </style>

    <script>
        function imageCarousel() {
            return {
                isOpen: false,
                images: [],
                activeIndex: 0,
                openCarousel(imageUrls) {
                    if (!imageUrls || imageUrls.length === 0) return;
                    this.images = imageUrls;
                    this.activeIndex = 0;
                    this.isOpen = true;
                },
                next() {
                    this.activeIndex = (this.activeIndex + 1) % this.images.length;
                },
                prev() {
                    this.activeIndex = (this.activeIndex - 1 + this.images.length) % this.images.length;
                }
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.$ && $.confirm) {
                $.confirm.defaults = $.extend({}, $.confirm.defaults, {
                    useBootstrap: false,
                    boxWidth: '40%',
                    theme: 'modern',
                    typeAnimated: true,
                });
            }

            const modal = document.getElementById('docModal');
            const backdrop = modal?.querySelector('.doc-backdrop');
            const closeBtn = modal?.querySelector('.doc-close');
            const titleEl = document.getElementById('docModalTitle');
            const imgEl = document.getElementById('docImage');
            const frameEl = document.getElementById('docFrame');

            const close = () => {
                if (!modal) return;
                modal.classList.remove('active');
                modal.setAttribute('aria-hidden', 'true');
                if (imgEl) {
                    imgEl.style.display = 'none';
                    imgEl.src = '';
                    imgEl.alt = '';
                }
                if (frameEl) {
                    frameEl.style.display = 'none';
                    frameEl.src = '';
                }
                if (titleEl) titleEl.textContent = '';
            };

            const open = (url, title) => {
                if (!modal) return;
                if (!url) return;
                const isPdf = url.toLowerCase().includes('.pdf');
                if (titleEl) titleEl.textContent = title || 'Attachment';
                if (isPdf) {
                    if (frameEl) {
                        frameEl.style.display = 'block';
                        frameEl.src = url;
                    }
                } else {
                    if (imgEl) {
                        imgEl.style.display = 'block';
                        imgEl.src = url;
                        imgEl.alt = title || 'Attachment';
                    }
                }
                modal.classList.add('active');
                modal.setAttribute('aria-hidden', 'false');
            };

            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.doc-link');
                if (btn) {
                    open(btn.dataset.url, btn.dataset.title);
                    return;
                }
                if (e.target === backdrop || e.target === closeBtn) {
                    close();
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') close();
            });

            $(document).on('click', '.approve-btn', function () {
                if ($(this).hasClass('complete-btn')) return;
                const id = $(this).data('rental-id');
                $.confirm({
                    title: 'Confirm Booking',
                    content: 'Are you sure you want to confirm this booking?',
                    type: 'green',
                    buttons: {
                        Confirm: {
                            btnClass: 'btn-green',
                            action: function(){
                                if (window.AARLoading) window.AARLoading.show('Confirming booking…', 'Updating status and sending email notification…');
                                $.ajax({
                                    url: '{{ route('rentals.confirm', ['rental' => '__ID__']) }}'.replace('__ID__', id),
                                    method: 'POST',
                                    data: { _token: '{{ csrf_token() }}' },
                                    success: function(res){
                                        if(res.success){
                                            location.reload();
                                        }
                                    },
                                    error: function(){
                                        if (window.AARLoading) window.AARLoading.hide();
                                    },
                                    complete: function(){
                                        if (window.AARLoading) window.AARLoading.hide();
                                    }
                                });
                            }
                        },
                        Cancel: function(){}
                    }
                });
            });

            $(document).on('click', '.complete-btn', function () {
                const id = $(this).data('rental-id');
                $.confirm({
                    title: 'Mark as Completed',
                    content: '' +
                        '<div style="color:#0f172a; font-weight: 700; margin-bottom: 10px;">Mark this trip as completed? Optionally enter the Actual Service Fee (or leave empty to use the estimated fee):</div>' +
                        '<input type="number" id="actualPriceInput" min="0" step="0.01" style="width:100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px;" placeholder="Actual final amount received...">' +
                        '<div style="font-size:0.8rem; color:#64748b; margin-top: 6px;">Entering no value defaults to the estimated service fee.</div>',
                    type: 'green',
                    buttons: {
                        Complete: {
                            btnClass: 'btn-green',
                            action: function(){
                                const actualPriceEl = document.getElementById('actualPriceInput');
                                const actualPrice = actualPriceEl ? actualPriceEl.value : '';

                                if (window.AARLoading) window.AARLoading.show('Completing travel…', 'Updating status and sending email notification…');
                                $.ajax({
                                    url: '{{ route('rentals.complete', ['rental' => '__ID__']) }}'.replace('__ID__', id),
                                    method: 'POST',
                                    data: { _token: '{{ csrf_token() }}', actual_price: actualPrice },
                                    success: function(res){
                                        if(res.success){
                                            location.reload();
                                        }
                                    },
                                    error: function(){
                                        if (window.AARLoading) window.AARLoading.hide();
                                    },
                                    complete: function(){
                                        if (window.AARLoading) window.AARLoading.hide();
                                    }
                                });
                            }
                        },
                        Cancel: function(){}
                    }
                });
            });

            $(document).on('click', '.cancel-btn', function () {
                const id = $(this).data('rental-id');
                $.confirm({
                    title: 'Reject Booking',
                    content: '' +
                        '<div style="color:#0f172a; font-weight: 700; margin-bottom: 10px;">Provide the reason for rejecting this booking:</div>' +
                        '<textarea id="rejectReasonInput" rows="4" style="width:100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px; resize: vertical;" placeholder="Type the rejection reason..."></textarea>' +
                        '<div id="rejectReasonError" style="display:none; margin-top: 8px; color:#b91c1c; font-weight: 800;"></div>',
                    type: 'red',
                    onContentReady: function () {
                        const el = document.getElementById('rejectReasonInput');
                        if (el) el.focus();
                    },
                    buttons: {
                        Reject: {
                            btnClass: 'btn-red',
                            action: function(){
                                const reasonEl = document.getElementById('rejectReasonInput');
                                const errEl = document.getElementById('rejectReasonError');
                                const reason = reasonEl ? String(reasonEl.value || '').trim() : '';
                                if (errEl) errEl.style.display = 'none';
                                if (!reason) {
                                    if (errEl) {
                                        errEl.textContent = 'Rejection reason is required.';
                                        errEl.style.display = 'block';
                                    }
                                    return false;
                                }
                                if (window.AARLoading) window.AARLoading.show('Processing booking rejection…', 'Updating status and sending email notification…');
                                $.ajax({
                                    url: '{{ route('rentals.cancel', ['rental' => '__ID__']) }}'.replace('__ID__', id),
                                    method: 'POST',
                                    data: { _token: '{{ csrf_token() }}', rejection_reason: reason },
                                    success: function(res){
                                        if(res.success){
                                            location.reload();
                                        }
                                    },
                                    error: function(){
                                        if (window.AARLoading) window.AARLoading.hide();
                                    },
                                    complete: function(){
                                        if (window.AARLoading) window.AARLoading.hide();
                                    }
                                });
                            }
                        },
                        Close: function(){}
                    }
                });
            });
        });
    </script>
</x-member-layout>
