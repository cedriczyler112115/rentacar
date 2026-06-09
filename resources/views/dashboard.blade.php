<x-member-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2>{{ __('My Bookings') }}</h2>
                <p style="color: #64748b; margin-top: 5px;">Manage your bookings and account here.</p>
            </div>
            <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
                <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></span>
                {{ __('Owner Rating') }}:
                @if(($myOwnerRatingCount ?? 0) > 0)
                    ★ {{ number_format((float)($myOwnerRatingAvg ?? 0), 1) }} ({{ (int)$myOwnerRatingCount }})
                @else
                    No reviews yet
                @endif
            </div>
        </div>
    </x-slot>

    <div class="container" style="padding: 0px 20px 40px 20px;" x-data="imageCarousel()">
        <!-- Filter Controls -->
        <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
            <div class="filter-container">
                <a href="{{ route('dashboard') }}" class="filter-btn {{ !request('status') ? 'active' : '' }}">
                    All Bookings <span class="filter-count">{{ $allCount }}</span>
                </a>
                <a href="{{ route('dashboard', ['status' => 'Pending']) }}" class="filter-btn {{ request('status') == 'Pending' ? 'active' : '' }}">
                    Pending <span class="filter-count">{{ $statusCounts['Pending'] ?? 0 }}</span>
                </a>
                <a href="{{ route('dashboard', ['status' => 'Confirmed']) }}" class="filter-btn {{ request('status') == 'Confirmed' ? 'active' : '' }}">
                    Confirmed <span class="filter-count">{{ $statusCounts['Confirmed'] ?? 0 }}</span>
                </a>
                <a href="{{ route('dashboard', ['status' => 'Completed']) }}" class="filter-btn {{ request('status') == 'Completed' ? 'active' : '' }}">
                    Completed <span class="filter-count">{{ $statusCounts['Completed'] ?? 0 }}</span>
                </a>
                <a href="{{ route('dashboard', ['status' => 'Cancelled']) }}" class="filter-btn {{ request('status') == 'Cancelled' ? 'active' : '' }}">
                    Cancelled by Client <span class="filter-count">{{ $statusCounts['Cancelled'] ?? 0 }}</span>
                </a>
                <a href="{{ route('dashboard', ['status' => 'Rejected']) }}" class="filter-btn {{ request('status') == 'Rejected' ? 'active' : '' }}">
                    Rejected <span class="filter-count">{{ $statusCounts['Rejected'] ?? 0 }}</span>
                </a>
            </div>
        </div>

        <!-- Booking List -->
        @if($bookings->count() > 0)
            <div style="display: grid; grid-template-columns: 1fr; gap: 30px;">
                @foreach($bookings as $booking)
                    <div class="booking-card">
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
                            <div class="booking-timestamp" style="display:flex; align-items:center; gap:10px;">
                                @php
                                    $ownerId = (int) ($booking->vehicle->user_id ?? 0);
                                    $ownerRating = $ownerId > 0 ? ($ownerRatings[$ownerId] ?? null) : null;
                                @endphp
                                @if($booking->status === 'Completed' && $ownerRating && (int)($ownerRating['count'] ?? 0) > 0)
                                    <span class="owner-rating-pill" title="Owner rating based on reviews">
                                        Owner Rating ★ {{ number_format((float)($ownerRating['avg'] ?? 0), 1) }} ({{ (int)($ownerRating['count'] ?? 0) }} reviews)
                                    </span>
                                @endif
                                @if($booking->status === 'Pending')
                                    <button type="button" class="cancel-booking-btn flex items-center gap-1" data-rental-id="{{ $booking->id }}" aria-label="Cancel Booking">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                        Cancel Booking
                                    </button>   
                                @endif

                                <span>Booked on {{ $booking->created_at->format('F j, Y g:i A') }}</span>
                            </div>
                        </div>
                        
                        <div class="booking-content">
                            <!-- Left Side: Vehicle Visuals -->
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
                                        <span class="label">Rental Duration</span>
                                        @php
                                            $from = \Carbon\Carbon::parse($booking->datetime_from);
                                            $to = \Carbon\Carbon::parse($booking->datetime_to);
                                            $totalHours = $from->diffInHours($to);
                                            $displayDays = floor($totalHours / 24);
                                            // Ensure minimum 1 day if it's less than 24h and no extra hours are recorded
                                            if ($displayDays == 0 && $booking->extra_hours == 0) $displayDays = 1;
                                        @endphp
                                        <span class="value">
                                            {{ $displayDays }} Day(s)
                                            @if($booking->extra_hours > 0)
                                                + {{ $booking->extra_hours }} Hr(s)
                                            @endif
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="label">Estemated Service Fee</span>
                                        <span class="value accent">₱{{ number_format($booking->estimated_price, 2) }}</span>
                                    </div>
                                    @if(!empty($booking->additional_message))
                                    <div class="info-item full-span">
                                        <span class="label">Message to Owner / Offers</span>
                                        <span class="value" style="white-space: pre-wrap; color: #0f172a; font-weight:700;">{{ $booking->additional_message }}</span>
                                    </div>
                                    @endif
                                </div>

                                @if($booking->status === 'Completed')
                                    <div class="review-box" id="reviewBox-{{ $booking->id }}">
                                        <div class="review-header">
                                            <div style="font-weight: 500; color: var(--primary);">Vehicle Review</div>
                                            <a href="{{ route('reviews.vehicle', ['vehicle' => $booking->vehicle_id]) }}" class="review-view-link" data-vehicle-id="{{ $booking->vehicle_id }}" data-vehicle-name="{{ $booking->vehicle->name }}">
                                                View All Reviews
                                            </a>
                                        </div>

                                        @if($booking->review)
                                            <div class="review-display">
                                                <div class="review-stars">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <span class="star {{ $i <= (int)$booking->review->rating ? 'filled' : '' }}">★</span>
                                                    @endfor
                                                    <span class="review-meta">{{ $booking->review->created_at?->format('M d, Y') }}</span>
                                                </div>
                                                <div class="review-comment">{{ $booking->review->comment }}</div>
                                            </div>
                                        @else
                                            <form class="review-form" data-rental-id="{{ $booking->id }}" data-owner-id="{{ (int)($booking->vehicle->user_id ?? 0) }}">
                                                @csrf
                                                <input type="hidden" name="rental_id" value="{{ $booking->id }}">
                                                <input type="hidden" name="rating" value="">
                                                <div class="review-stars-picker" role="radiogroup" aria-label="Rating">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <button type="button" class="star-btn" data-value="{{ $i }}" aria-label="{{ $i }} star">{{ $i <= 5 ? '★' : '' }}</button>
                                                    @endfor
                                                </div>
                                                <textarea name="comment" rows="3" required placeholder="Write your review..." class="review-text"></textarea>
                                                <div class="review-actions">
                                                    <div class="review-msg" aria-live="polite"></div>
                                                    <button type="submit" class="btn btn-primary review-submit">Submit Review</button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Right Side: Detailed Specifications -->
                            <div class="vehicle-specs">
                                <div class="specs-header">
                                    <h3 class="vehicle-name">{{ $booking->vehicle->name }}</h3>
                                    <span class="brand-tag">{{ $booking->vehicle->libBrand->name ?? 'N/A' }}</span>
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
                                    <!-- Left Side: Computation -->
                                    <div class="computation-box">
                                        <div class="details-header">Estimated Service Fee Computation</div>
                                        <div class="computation-grid">
                                            <div class="computation-item">
                                                <span class="c-label">Destination Charge ({{ $booking->municipality }}, {{ $booking->province }})</span>
                                                <span class="c-value">₱{{ number_format($booking->destination_price, 2) }} </span>
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

                                    <!-- Right Side: Itinerary -->
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
                                                            @if($log->action === 'rejected' && ($log->new_values['rejection_reason'] ?? null))
                                                                <div style="margin-top: 4px; color:#be123c; font-weight: 800;">Reason: {{ $log->new_values['rejection_reason'] }}</div>
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

            <!-- Pagination -->
            <div style="margin-top: 40px;">
                {{ $bookings->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 17h6"/><path d="M10 13h4"/><path d="M12 2v2"/><path d="M12 8v2"/><path d="M12 14v2"/><path d="M12 20v2"/><rect x="5" y="2" width="14" height="20" rx="2"/></svg>
                </div>
                <h3>No Bookings Found</h3>
                <p>We couldn't find any bookings with the status "{{ request('status', 'All') }}".</p>
                <a href="{{ route('vehicles.index') }}" class="btn btn-primary">Start Browsing Vehicles</a>
            </div>
        @endif

        <!-- Image Carousel Modal -->
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

        <div id="reviewsModal" class="reviews-modal" aria-hidden="true">
            <div class="reviews-backdrop"></div>
            <div class="reviews-panel" role="dialog" aria-modal="true" aria-labelledby="reviewsModalTitle">
                <div class="reviews-header">
                    <div>
                        <div id="reviewsModalTitle" class="reviews-title"></div>
                        <div class="reviews-sub" id="reviewsModalSub"></div>
                    </div>
                    <button type="button" class="reviews-close" aria-label="Close reviews">&times;</button>
                </div>
                <div class="reviews-body">
                    <div id="reviewsModalState" class="reviews-state">Loading…</div>
                    <div id="reviewsModalList" class="reviews-list" style="display:none;"></div>
                    <div style="margin-top: 12px; display:flex; justify-content:flex-end;">
                        <button type="button" id="reviewsLoadMoreBtn" class="btn btn-outline" style="padding: 10px 14px; font-size: 0.95rem; display:none;">Load more</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .filter-container { display: flex; gap: 10px; background: white; padding: 6px; border-radius: 40px; box-shadow: var(--shadow-sm); border: 1px solid #e2e8f0; overflow-x: auto; -ms-overflow-style: none; scrollbar-width: none; }
        .filter-container::-webkit-scrollbar { display: none; }
        .filter-btn { padding: 10px 24px; border-radius: 30px; font-weight: 600; color: #64748b; transition: var(--transition); white-space: nowrap; font-size: 0.95rem; }
        .filter-btn.active { background: var(--primary); color: white; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2); }
        .filter-btn:not(.active):hover { background: #f1f5f9; color: var(--primary); }
        .filter-count { background: #e2e8f0; color: #475569; padding: 2px 8px; border-radius: 10px; font-size: 0.8rem; font-weight: 700; margin-left: 8px; }
        .filter-btn.active .filter-count { background: var(--accent); color: var(--primary); }

        .booking-card { background: white; border-radius: 16px; box-shadow: var(--shadow-sm); border: 1px solid #e2e8f0; overflow: hidden; transition: var(--transition); }
        .booking-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
        
        .booking-header { padding: 16px 24px; background: #f8fafc; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; }
        .header-main { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .booking-ref { font-weight: 800; color: var(--primary); letter-spacing: 0.5px; font-size: 1.05rem; }
        .booking-timestamp { font-size: 0.85rem; color: #94a3b8; font-weight: 500; }
        .owner-rating-pill { display:inline-flex; align-items:center; gap: 8px; padding: 6px 12px; border-radius: 999px; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.25); color: #b45309; font-weight: 900; font-size: 0.82rem; white-space: nowrap; }
        
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
        
        /* Left Visual Column */
        .vehicle-visual { display: flex; flex-direction: column; gap: 20px; }
        .main-image-wrapper { width: 100%; height: 200px; border-radius: 12px; overflow: hidden; background: #f1f5f9; border: 1px solid #e2e8f0; cursor: pointer; }
        .main-image-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: var(--transition); }
        .booking-card:hover .main-image-wrapper img { transform: scale(1.05); }
        .placeholder-img { height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; gap: 10px; font-size: 0.85rem; }
        
        .quick-info { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .info-item { background: #f8fafc; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 4px; }
        .info-item.full-span { grid-column: 1 / -1; }
        .info-item .label { font-size: 0.7rem; text-transform: uppercase; font-weight: 700; color: #94a3b8; letter-spacing: 0.5px; }
        .info-item .value { font-weight: 800; color: var(--primary); font-size: 0.95rem; }
        .info-item .value.accent { color: var(--accent); }

        .cancel-booking-btn { background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 6px 10px; border-radius: 10px; font-weight: 900; cursor: pointer; }
        .cancel-booking-btn:hover { filter: brightness(1.02); }

        .review-box { margin-top: 0px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; }
        .review-header { display:flex; align-items:center; justify-content:space-between; gap: 10px; margin-bottom: 10px; }
        .review-view-link { background: transparent; border: 0px solid #e2e8f0; padding: 8px 12px; border-radius: 999px; font-weight: 500; font-size: 0.85rem; color: var(--primary); cursor: pointer; display:inline-flex; align-items:center; }
        .review-view-link:hover { border-color: var(--accent); color: var(--accent); }
        .review-stars { display:flex; align-items:center; gap: 6px; flex-wrap: wrap; }
        .review-stars .star { font-size: 1.05rem; color: #cbd5e1; }
        .review-stars .star.filled { color: var(--accent); }
        .review-meta { margin-left: 6px; color: #94a3b8; font-weight: 300; font-size: 0.82rem; }
        .review-comment { margin-top: 0px; color: #0f172a; font-weight: 500; font-size: 0.85rem; white-space: pre-wrap; }
        .review-stars-picker { display:flex; gap: 6px; }
        .star-btn { width: 40px; height: 40px; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; color: #cbd5e1; font-size: 1.25rem; font-weight: 900; cursor: pointer; transition: var(--transition); }
        .star-btn:hover, .star-btn.hovered { border-color: rgba(245, 158, 11, 0.5); color: #f59e0b; transform: translateY(-1px); }
        .star-btn.selected { border-color: rgba(245, 158, 11, 0.5); background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
        .review-text { width: 100%; margin-top: 10px; padding: 10px; border-radius: 12px; border: 1px solid #cbd5e1; font-weight: 700; resize: vertical; }
        .review-text:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2); }
        .review-actions { margin-top: 10px; display:flex; justify-content:space-between; align-items:center; gap: 10px; flex-wrap: wrap; }
        .review-msg { font-weight: 900; font-size: 0.9rem; }
        .review-msg.error { color: #b91c1c; }
        .review-msg.success { color: #16a34a; }
        .review-submit[disabled] { opacity: 0.7; cursor: not-allowed; }

        /* Right Specs Column */
        .vehicle-specs { display: flex; flex-direction: column; gap: 24px; }
        .specs-header { display: flex; align-items: flex-end; gap: 12px; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; }
        .vehicle-name { font-size: 1.6rem; font-weight: 800; color: var(--primary); margin: 0; line-height: 1.2; }
        .brand-tag { background: var(--accent) !important; color: white; padding: 4px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        
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
        .computation-box, .itinerary-box, .logs-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; }
        .details-header { font-size: 0.8rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px; }
        
        .computation-grid { display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px; }
        .computation-item { display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem; }
        .c-label { color: #64748b; }
        .c-value { font-weight: 700; color: var(--primary); }

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

        .reviews-modal { position: fixed; inset: 0; z-index: 2300; display: none; align-items: center; justify-content: center; padding: 20px; }
        .reviews-modal.active { display: flex; }
        .reviews-backdrop { position: absolute; inset: 0; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px); }
        .reviews-panel { position: relative; z-index: 1; width: 100%; max-width: 860px; background: white; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 18px 35px rgba(0,0,0,0.25); }
        .reviews-header { display:flex; align-items:flex-start; justify-content:space-between; gap: 10px; padding: 14px 16px; background: #0f172a; color: white; }
        .reviews-title { font-weight: 900; font-size: 1.05rem; }
        .reviews-sub { margin-top: 4px; color: rgba(226, 232, 240, 0.85); font-weight: 800; font-size: 0.85rem; }
        .reviews-close { background: none; border: none; color: white; font-size: 2rem; line-height: 1; cursor: pointer; opacity: 0.85; transition: var(--transition); }
        .reviews-close:hover { opacity: 1; transform: rotate(90deg); }
        .reviews-body { padding: 14px 16px; background: #f8fafc; }
        .reviews-state { color: #64748b; font-weight: 900; padding: 12px; }
        .reviews-list { display:flex; flex-direction:column; gap: 10px; }
        .review-row { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; }
        .review-row-top { display:flex; justify-content:space-between; align-items:flex-start; gap: 10px; flex-wrap: wrap; }
        .review-row-name { font-weight: 900; color: #0f172a; }
        .review-row-date { font-weight: 900; color: #94a3b8; font-size: 0.85rem; }
        .review-row-stars { display:flex; gap: 4px; color: #cbd5e1; font-weight: 900; }
        .review-row-stars .filled { color: var(--accent); }
        .review-row-comment { margin-top: 8px; color: #0f172a; font-weight: 400; font-size: 0.85rem; white-space: pre-wrap; }

        .booking-footer { padding: 16px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0; }
        .time-block { display: flex; align-items: center; gap: 40px; }
        .time-item { display: flex; flex-direction: column; gap: 4px; }
        .time-divider { height: 30px; width: 1px; background: #cbd5e1; }
        .t-label { font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
        .t-value { font-size: 0.95rem; font-weight: 800; color: var(--primary); }

        .empty-state { text-align: center; padding: 80px 40px; background: white; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .empty-icon { color: #cbd5e1; margin-bottom: 24px; }
        .empty-state h3 { font-size: 1.5rem; font-weight: 800; color: var(--primary); margin-bottom: 12px; }
        .empty-state p { color: #64748b; margin-bottom: 32px; }

        /* Carousel Styles */
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

        @media (max-width: 1024px) {
            .booking-content { grid-template-columns: 1fr; gap: 24px; }
            .vehicle-visual { flex-direction: row; align-items: center; }
            .main-image-wrapper { width: 200px; height: 140px; flex-shrink: 0; }
            .quick-info { flex-grow: 1; }
            .specs-grid { grid-template-columns: 1fr 1fr; }
            .details-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .booking-header { flex-direction: column; align-items: flex-start; gap: 12px; }
            .vehicle-visual { flex-direction: column; align-items: stretch; }
            .main-image-wrapper { width: 100%; height: 200px; }
            .specs-grid { grid-template-columns: 1fr; }
            .details-grid { grid-template-columns: 1fr; }
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
            const modal = document.getElementById('docModal');
            const backdrop = modal ? modal.querySelector('.doc-backdrop') : null;
            const closeBtn = modal ? modal.querySelector('.doc-close') : null;
            const frameEl = document.getElementById('docFrame');
            const imgEl = document.getElementById('docImage');
            const titleEl = document.getElementById('docModalTitle');

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

            if (window.$ && $.confirm) {
                $.confirm.defaults = $.extend({}, $.confirm.defaults, {
                    useBootstrap: false,
                    boxWidth: '40%',
                    theme: 'modern',
                    typeAnimated: true,
                });
            }

            $(document).on('click', '.cancel-booking-btn', function () {
                const id = $(this).data('rental-id');
                $.confirm({
                    title: 'Cancel Booking',
                    content: 'Are you sure you want to cancel this booking?',
                    type: 'red',
                    buttons: {
                        Cancel_Booking: {
                            btnClass: 'btn-red',
                            action: function(){
                                $.ajax({
                                    url: '{{ route('rentals.cancel_by_renter', ['rental' => '__ID__']) }}'.replace('__ID__', id),
                                    method: 'POST',
                                    data: { _token: '{{ csrf_token() }}' },
                                    success: function(res){
                                        if(res.success){
                                            location.reload();
                                        }
                                    }
                                });
                            }
                        },
                        Close: function(){}
                    }
                });
            });

            if (window.$) {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                if (csrf) {
                    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrf } });
                }
            }

            const fmtInt = (n) => {
                if (typeof n === 'number' && Number.isFinite(n)) return n.toLocaleString();
                const v = Number(n);
                if (Number.isFinite(v)) return v.toLocaleString();
                return '0';
            };

            const setStars = (formEl, value) => {
                const buttons = formEl.querySelectorAll('.star-btn');
                buttons.forEach((btn) => {
                    const v = parseInt(btn.getAttribute('data-value') || '0', 10);
                    btn.classList.toggle('selected', v <= value);
                });
                const ratingInput = formEl.querySelector('input[name="rating"]');
                if (ratingInput) ratingInput.value = value ? String(value) : '';
            };

            document.addEventListener('pointerover', (e) => {
                const btn = e.target.closest('.star-btn');
                if (!btn) return;
                const form = btn.closest('.review-form');
                if (!form) return;
                const value = parseInt(btn.getAttribute('data-value') || '0', 10);
                form.querySelectorAll('.star-btn').forEach((b) => {
                    const v = parseInt(b.getAttribute('data-value') || '0', 10);
                    b.classList.toggle('hovered', v <= value);
                });
            });

            document.addEventListener('pointerout', (e) => {
                const btn = e.target.closest('.star-btn');
                if (!btn) return;
                const form = btn.closest('.review-form');
                if (!form) return;
                form.querySelectorAll('.star-btn').forEach((b) => b.classList.remove('hovered'));
            });

            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.star-btn');
                if (!btn) return;
                const form = btn.closest('.review-form');
                if (!form) return;
                const value = parseInt(btn.getAttribute('data-value') || '0', 10);
                setStars(form, value);
            });

            const renderReviewDisplay = (review) => {
                const wrap = document.createElement('div');
                wrap.className = 'review-display';

                const starsRow = document.createElement('div');
                starsRow.className = 'review-stars';
                for (let i = 1; i <= 5; i++) {
                    const s = document.createElement('span');
                    s.className = 'star' + (i <= (review.rating || 0) ? ' filled' : '');
                    s.textContent = '★';
                    starsRow.appendChild(s);
                }
                const meta = document.createElement('span');
                meta.className = 'review-meta';
                meta.textContent = review.created_at ? new Date(review.created_at).toLocaleDateString() : '';
                starsRow.appendChild(meta);

                const comment = document.createElement('div');
                comment.className = 'review-comment';
                comment.textContent = review.comment || '';

                wrap.appendChild(starsRow);
                wrap.appendChild(comment);
                return wrap;
            };

            document.addEventListener('submit', async (e) => {
                const form = e.target.closest('.review-form');
                if (!form) return;
                e.preventDefault();

                const msg = form.querySelector('.review-msg');
                const submitBtn = form.querySelector('.review-submit');
                const rentalId = form.getAttribute('data-rental-id');
                const ownerId = parseInt(form.getAttribute('data-owner-id') || '0', 10);

                const rating = parseInt(form.querySelector('input[name="rating"]')?.value || '0', 10);
                const comment = (form.querySelector('textarea[name="comment"]')?.value || '').trim();

                if (msg) {
                    msg.classList.remove('error', 'success');
                    msg.textContent = '';
                }

                if (!rating || rating < 1 || rating > 5) {
                    if (msg) {
                        msg.classList.add('error');
                        msg.textContent = 'Please select a star rating.';
                    }
                    return;
                }
                if (!comment) {
                    if (msg) {
                        msg.classList.add('error');
                        msg.textContent = 'Please write a short review.';
                    }
                    return;
                }

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Submitting...';
                }

                try {
                    const res = await fetch('{{ route('reviews.store') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        },
                        body: JSON.stringify({
                            rental_id: rentalId,
                            rating,
                            comment,
                        }),
                    });

                    const payload = await res.json().catch(() => null);
                    if (!res.ok) {
                        const m = payload?.message || 'Unable to submit review.';
                        throw new Error(m);
                    }

                    const box = document.getElementById('reviewBox-' + rentalId);
                    if (box) {
                        const existing = box.querySelector('.review-form');
                        if (existing) {
                            existing.replaceWith(renderReviewDisplay(payload.review));
                        }
                    }

                    if (ownerId > 0 && payload?.owner) {
                        const badge = document.querySelector('#reviewBox-' + rentalId)?.closest('.booking-card')?.querySelector('.owner-rating-pill');
                        const avg = Number(payload.owner.avg_rating || 0);
                        const cnt = Number(payload.owner.total_reviews || 0);
                        const text = 'Owner Rating ★ ' + avg.toFixed(1) + ' (' + cnt + ')';
                        if (badge) {
                            badge.textContent = text;
                        } else {
                            const ts = document.querySelector('#reviewBox-' + rentalId)?.closest('.booking-card')?.querySelector('.booking-timestamp');
                            if (ts) {
                                const span = document.createElement('span');
                                span.className = 'owner-rating-pill';
                                span.title = 'Owner rating based on reviews';
                                span.textContent = text;
                                ts.insertBefore(span, ts.firstChild);
                            }
                        }
                    }

                    if (msg) {
                        msg.classList.add('success');
                        msg.textContent = 'Review submitted.';
                    }
                } catch (err) {
                    if (msg) {
                        msg.classList.add('error');
                        msg.textContent = err?.message || 'Unable to submit review.';
                    }
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Submit Review';
                    }
                }
            });

            const reviewsModal = document.getElementById('reviewsModal');
            const reviewsBackdrop = reviewsModal ? reviewsModal.querySelector('.reviews-backdrop') : null;
            const reviewsClose = reviewsModal ? reviewsModal.querySelector('.reviews-close') : null;
            const reviewsTitle = document.getElementById('reviewsModalTitle');
            const reviewsSub = document.getElementById('reviewsModalSub');
            const reviewsState = document.getElementById('reviewsModalState');
            const reviewsList = document.getElementById('reviewsModalList');
            const loadMoreBtn = document.getElementById('reviewsLoadMoreBtn');

            let nextReviewsUrl = null;

            const openReviewsModal = () => {
                if (!reviewsModal) return;
                reviewsModal.classList.add('active');
                reviewsModal.setAttribute('aria-hidden', 'false');
            };

            const closeReviewsModal = () => {
                if (!reviewsModal) return;
                reviewsModal.classList.remove('active');
                reviewsModal.setAttribute('aria-hidden', 'true');
                nextReviewsUrl = null;
                if (reviewsState) {
                    reviewsState.style.display = 'block';
                    reviewsState.textContent = '';
                }
                if (reviewsList) {
                    reviewsList.style.display = 'none';
                    reviewsList.innerHTML = '';
                }
                if (loadMoreBtn) loadMoreBtn.style.display = 'none';
            };

            const renderReviews = (items, append) => {
                if (!reviewsList) return;
                if (!append) reviewsList.innerHTML = '';
                items.forEach((r) => {
                    const row = document.createElement('div');
                    row.className = 'review-row';

                    const top = document.createElement('div');
                    top.className = 'review-row-top';

                    const left = document.createElement('div');
                    const nm = document.createElement('div');
                    nm.className = 'review-row-name';
                    nm.textContent = r.reviewer?.name || 'User';
                    const stars = document.createElement('div');
                    stars.className = 'review-row-stars';
                    for (let i = 1; i <= 5; i++) {
                        const s = document.createElement('span');
                        s.className = i <= (r.rating || 0) ? 'filled' : '';
                        s.textContent = '★';
                        stars.appendChild(s);
                    }
                    left.appendChild(nm);
                    left.appendChild(stars);

                    const right = document.createElement('div');
                    right.className = 'review-row-date';
                    right.textContent = r.created_at ? new Date(r.created_at).toLocaleDateString() : '';

                    top.appendChild(left);
                    top.appendChild(right);

                    const comment = document.createElement('div');
                    comment.className = 'review-row-comment';
                    comment.textContent = r.comment || '';

                    row.appendChild(top);
                    row.appendChild(comment);
                    reviewsList.appendChild(row);
                });
            };

            const fetchReviews = async (url, append) => {
                if (!reviewsState || !reviewsList) return;
                reviewsState.style.display = 'block';
                reviewsState.textContent = append ? 'Loading more…' : 'Loading…';
                reviewsList.style.display = append ? 'block' : 'none';
                if (loadMoreBtn) loadMoreBtn.style.display = 'none';

                try {
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) throw new Error('Unable to load reviews.');
                    const data = await res.json();
                    nextReviewsUrl = data.next_page_url || null;

                    if (reviewsTitle) {
                        const plate = data?.vehicle?.license_plate ? (' • ' + data.vehicle.license_plate) : '';
                        reviewsTitle.textContent = (data?.vehicle?.name || 'Vehicle') + plate;
                    }
                    if (reviewsSub) {
                        reviewsSub.textContent = 'Average Rating ★ ' + Number(data.avg_rating || 0).toFixed(1) + ' in ' + fmtInt(Number(data.total_reviews || 0)) + ' reviews';
                    }

                    renderReviews(Array.isArray(data.reviews) ? data.reviews : [], append);
                    reviewsState.style.display = 'none';
                    reviewsList.style.display = 'block';
                    if (loadMoreBtn) loadMoreBtn.style.display = nextReviewsUrl ? 'inline-block' : 'none';
                } catch (err) {
                    reviewsState.style.display = 'block';
                    reviewsState.textContent = err?.message || 'Unable to load reviews.';
                }
            };

            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.review-view-link');
                if (!btn) return;
                e.preventDefault();
                const vehicleId = btn.getAttribute('data-vehicle-id');
                if (!vehicleId) return;
                openReviewsModal();
                const url = '{{ route('reviews.vehicle', ['vehicle' => '__ID__']) }}'.replace('__ID__', vehicleId);
                fetchReviews(url, false);
            });

            loadMoreBtn?.addEventListener('click', () => {
                if (nextReviewsUrl) fetchReviews(nextReviewsUrl, true);
            });

            document.addEventListener('click', (e) => {
                if (e.target === reviewsBackdrop || e.target === reviewsClose) closeReviewsModal();
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeReviewsModal();
            });
        });
    </script>
    </style>
</x-member-layout>
