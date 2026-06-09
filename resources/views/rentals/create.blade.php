<x-member-layout>
    <x-slot name="header">
        <h2 class="font-semibold" style="font-size: 1.5rem; color: var(--primary);">
            {{ __('Complete Your Booking') }}
        </h2>
    </x-slot>

    <div class="container" style="padding: 40px 20px; max-width: 900px; margin: 0 auto;">
        
        <div style="background: white; border-radius: 12px; box-shadow: var(--shadow-md); overflow: hidden;">
            
            <div style="background: var(--primary); color: white; padding: 30px; display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                @php $primaryImage = $vehicle->images->where('is_primary', true)->first() ?? $vehicle->images->first(); @endphp
                <div onclick="openBookingImagesModal()" title="Click to view images" style="width: 140px; height: 90px; background: #334155; border-radius: 8px; overflow: hidden; flex-shrink: 0; box-shadow: inset 0 2px 4px rgba(0,0,0,0.5); cursor: pointer;">
                    @if($primaryImage)
                        <img src="{{ Storage::url($primaryImage->image_path) }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="display:flex; height:100%; align-items:center; justify-content:center; color:#94a3b8;">No Image</div>
                    @endif
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.8rem; font-weight: 700;">{{ $vehicle->name }}</h3>
                    <p style="margin: 5px 0 0 0; color: #cbd5e1; font-size: 1.05rem;">
                        {{ $vehicle->libBrand->name ?? 'Unknown Brand' }} &bull; {{ $vehicle->year_model ?? 'Year Model' }} &bull; {{ $vehicle->libTransmission->name ?? 'Auto' }}
                    </p>
                </div>
                <div style="margin-left: auto; text-align: right;">
                    <div style="font-size: 0.9rem; color: #94a3b8; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">Start Rate</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: var(--accent);">
                        ₱<span id="base_price">{{ number_format($vehicle->price_per_day, 2, '.', '') }}</span><span style="font-size: 1rem; color: #cbd5e1;">/day</span>
                    </div>
                </div>
            </div>

            <form id="bookingForm" action="{{ route('rentals.store', $enc_id) }}" method="POST" enctype="multipart/form-data" style="padding: 40px;">
                @csrf
                
                @if ($errors->any())
                    <div style="background: #fee2e2; border-left: 4px solid #ef4444; color: #b91c1c; padding: 15px 20px; border-radius: 0 8px 8px 0; margin-bottom: 25px;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li style="font-weight: 500;">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <h4 style="font-size: 1.25rem; font-weight: 700; color: var(--primary); margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">Vehicle Specifications</h4>

                @php
                    $availabilityParts = [];
                    $ad = $vehicle->booked_dates;
                    if (is_array($ad)) {
                        foreach ($ad as $item) {
                            if (is_string($item)) {
                                try {
                                    $availabilityParts[] = \Carbon\Carbon::createFromFormat('Y-m-d', $item)->format('F j, Y');
                                } catch (\Throwable $e) {
                                }
                                continue;
                            }
                            if (is_array($item) && isset($item['start'], $item['end'])) {
                                try {
                                    $start = \Carbon\Carbon::createFromFormat('Y-m-d', $item['start']);
                                    $end = \Carbon\Carbon::createFromFormat('Y-m-d', $item['end']);
                                    if ($start->year === $end->year && $start->month === $end->month) {
                                        $availabilityParts[] = $start->format('F j') . '-' . $end->format('j, Y');
                                    } elseif ($start->year === $end->year) {
                                        $availabilityParts[] = $start->format('F j') . '-' . $end->format('F j, Y');
                                    } else {
                                        $availabilityParts[] = $start->format('F j, Y') . '-' . $end->format('F j, Y');
                                    }
                                } catch (\Throwable $e) {
                                }
                            }
                        }
                    }
                @endphp

                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; margin-bottom: 35px;">
                    <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px;">
                            <div style="font-size: 0.72rem; color:#64748b; font-weight: 800; letter-spacing: .06em; text-transform: uppercase;">Type</div>
                            <div style="font-weight: 900; color: #0f172a; margin-top: 4px;">{{ $vehicle->libType->name ?? 'N/A' }}</div>
                        </div>
                        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px;">
                            <div style="font-size: 0.72rem; color:#64748b; font-weight: 800; letter-spacing: .06em; text-transform: uppercase;">Transmission</div>
                            <div style="font-weight: 900; color: #0f172a; margin-top: 4px;">{{ $vehicle->libTransmission->name ?? 'N/A' }}</div>
                        </div>
                        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px;">
                            <div style="font-size: 0.72rem; color:#64748b; font-weight: 800; letter-spacing: .06em; text-transform: uppercase;">Fuel Type</div>
                            <div style="font-weight: 900; color: #0f172a; margin-top: 4px;">{{ $vehicle->libFuelType->name ?? 'N/A' }}</div>
                        </div>
                        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px;">
                            <div style="font-size: 0.72rem; color:#64748b; font-weight: 800; letter-spacing: .06em; text-transform: uppercase;">Seating</div>
                            <div style="font-weight: 900; color: #0f172a; margin-top: 4px;">{{ $vehicle->seating_capacity ?? 'N/A' }} Seats</div>
                        </div>
                        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px;">
                            <div style="font-size: 0.72rem; color:#64748b; font-weight: 800; letter-spacing: .06em; text-transform: uppercase;">Color</div>
                            <div style="font-weight: 900; color: #0f172a; margin-top: 4px;">{{ $vehicle->color ?? 'N/A' }}</div>
                        </div>
                        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px;">
                            <div style="font-size: 0.72rem; color:#64748b; font-weight: 800; letter-spacing: .06em; text-transform: uppercase;">Displacement</div>
                            <div style="font-weight: 900; color: #0f172a; margin-top: 4px;">{{ $vehicle->displacement ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div style="margin-top: 14px; background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="font-size: 0.72rem; color:#64748b; font-weight: 800; letter-spacing: .06em; text-transform: uppercase;">Booked Dates</div>
                            <div style="font-weight: 900; color: #0f172a;">
                                {{ count($availabilityParts) > 0 ? 'Booked schedule' : 'No booked dates' }}
                            </div>
                        </div>
                        @if(count($availabilityParts) > 0)
                            <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top: 10px;">
                                @foreach($availabilityParts as $p)
                                    <span style="background:#eef2ff; border: 1px solid #c7d2fe; color:#1e3a8a; padding: 6px 10px; border-radius: 999px; font-weight: 800; font-size: 0.85rem;">{{ $p }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <h4 style="font-size: 1.25rem; font-weight: 700; color: var(--primary); margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">Travel Details</h4>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Region <span style="color: #ef4444;">*</span></label>
                        <select name="region" id="region" required style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem;" onchange="updateProvinces()">
                            <option value="">Select Region</option>
                            @foreach($municipalities as $region => $provinces)
                                <option value="{{ $region }}">{{ $region }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Province <span style="color: #ef4444;">*</span></label>
                        <select name="province" id="province" required style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem;" onchange="updateMunicipalities()" disabled>
                            <option value="">Select Province</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Municipality <span style="color: #ef4444;">*</span></label>
                        <select name="municipality" id="municipality" required style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem;" onchange="calculatePrice()" disabled>
                            <option value="">Select Municipality</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Pickup Date & Time <span style="color: #ef4444;">*</span></label>
                        <input type="datetime-local" id="datetime_from" name="datetime_from" required style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem;" value="{{ old('datetime_from') }}" onchange="calculatePrice()">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Return Date & Time <span style="color: #ef4444;">*</span></label>
                        <input type="datetime-local" id="datetime_to" name="datetime_to" required style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem;" value="{{ old('datetime_to') }}" onchange="calculatePrice()">
                    </div>
                </div>

                <div id="bookingCalendarWidget" style="margin-top: -10px; margin-bottom: 30px; background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                    <div style="display:flex; justify-content:space-between; gap: 10px; align-items:center; flex-wrap: wrap;">
                        <div>
                            <div style="font-weight: 900; color: var(--primary); font-size: 1.05rem;">Booking Calendar</div>
                            <div style="margin-top: 6px; color:#64748b; font-weight: 800; font-size: 0.9rem;">Booked dates are disabled. Select an available start and end date.</div>
                        </div>
                        <div style="display:flex; gap: 8px; align-items:center;">
                            <button type="button"
                                class="btn btn-outline"
                                style="padding: 10px 12px;"
                                data-aar-calendar-open="1"
                                data-title="{{ $vehicle->name }} Calendar"
                                data-vehicle-id="{{ (int)$vehicle->id }}"
                                data-events-url="{{ route('vehicles.calendar.events', $vehicle) }}"
                                data-owner-booking-url="{{ route('booking.calendar.owner-bookings.store') }}"
                                data-can-owner-book="{{ (int)auth()->id() === (int)$vehicle->user_id ? '1' : '0' }}">
                                Open Full Calendar
                            </button>
                            <button type="button" id="calPrevBtn" class="btn btn-outline" style="padding: 10px 12px;">Prev</button>
                            <div id="calMonthLabel" style="font-weight: 900; color:#0f172a; min-width: 160px; text-align:center;"></div>
                            <button type="button" id="calNextBtn" class="btn btn-outline" style="padding: 10px 12px;">Next</button>
                        </div>
                    </div>

                    <div style="margin-top: 12px; display:flex; gap: 12px; flex-wrap:wrap; align-items:center;">
                        <div style="display:inline-flex; align-items:center; gap: 8px;">
                            <span style="width: 12px; height: 12px; border-radius: 4px; background: #10b981; display:inline-block;"></span>
                            <span style="font-weight: 800; color:#475569; font-size: 0.9rem;">Available</span>
                        </div>
                        <div style="display:inline-flex; align-items:center; gap: 8px;">
                            <span style="width: 12px; height: 12px; border-radius: 4px; background: #ef4444; display:inline-block;"></span>
                            <span style="font-weight: 800; color:#475569; font-size: 0.9rem;">Booked</span>
                        </div>
                        <div style="display:inline-flex; align-items:center; gap: 8px;">
                            <span style="width: 12px; height: 12px; border-radius: 4px; background: rgba(245,158,11,0.85); display:inline-block;"></span>
                            <span style="font-weight: 800; color:#475569; font-size: 0.9rem;">Selected</span>
                        </div>
                        <div id="bookingCalendarError" style="display:none; margin-left:auto; color:#b91c1c; font-weight: 900;"></div>
                    </div>

                    <div style="margin-top: 12px; overflow:auto; border: 1px solid #e2e8f0; border-radius: 14px; background: white;">
                        <div id="calGrid" style="min-width: 840px; padding: 10px; display:grid; grid-template-columns: repeat(7, minmax(120px, 1fr)); gap: 8px;"></div>
                    </div>
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Pickup Location <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="pickup_location" name="pickup_location" required placeholder="e.g., Main Branch, Butuan City" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem;" value="{{ old('pickup_location') }}">
                </div>

                <h4 style="font-size: 1.25rem; font-weight: 700; color: var(--primary); margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; margin-top: 40px;">Booking Summary</h4>

                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0; margin-bottom: 30px; overflow: hidden;">
                    <div style="padding: 20px; border-bottom: 1px solid #e2e8f0;">
                        <div style="display:flex; gap:10px; align-items:flex-start; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.25); color: #7c2d12; padding: 12px 14px; border-radius: 10px; margin-bottom: 14px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#b45309" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                            <div style="font-weight: 700; font-size: 0.9rem; line-height: 1.3;">
                                Destination charge is based on travel areas. The starting rate above usually for city drive rate.
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span style="color: #64748b;">Destination Charge (<span id="summary_municipality">None</span>)</span>
                            <span style="font-weight: 600;">₱<span id="summary_destination_price">0.00</span></span>
                        </div>
                        <div id="carwash_row" style="display: none; justify-content: space-between; margin-bottom: 10px;">
                            <span style="color: #64748b;">Carwash Fee</span>
                            <span style="font-weight: 600;">₱<span id="summary_carwash_fee">0.00</span></span>
                        </div>
                        <div id="extra_hours_row" style="display: none; justify-content: space-between; margin-bottom: 10px;">
                            <span style="color: #64748b;">Extra Hours (<span id="summary_extra_hours">0</span> hrs)</span>
                            <span style="font-weight: 600;">₱<span id="summary_extra_hours_fee">0.00</span></span>
                        </div>
                    </div>
                    <div style="background: #f1f5f9; padding: 20px; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <div style="font-weight: 600; color: #64748b; margin-bottom: 5px; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px;">Estimated Service Cost</div>
                            <div style="font-size: 2.2rem; font-weight: 800; color: var(--accent);">₱<span id="computed_price">0.00</span></div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 600; color: #64748b; margin-bottom: 5px; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px;">Rental Duration</div>
                            <div id="duration_display" style="font-size: 1.3rem; font-weight: 700; color: var(--primary);">0 Days</div>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Downpayment Amount Provided (Optional)</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-weight: 700;">₱</span>
                            <input type="number" step="0.01" name="downpayment_amount" placeholder="0.00" style="width: 100%; padding: 12px 15px 12px 35px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem;" value="{{ old('downpayment_amount') }}">
                        </div>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Proof of Payment (Attachments)</label>
                        <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf" style="width: 100%; padding: 9px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; background: white; cursor: pointer;">
                        <p style="margin: 6px 0 0 0; font-size: 0.8rem; color: #64748b;">Upload screenshots or PDF receipts. Max size 5MB each.</p>
                    </div>
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Upload Drivers License</label>
                    <input type="file" name="drivers_license" accept=".jpg,.jpeg,.png,.pdf" style="width: 100%; padding: 9px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; background: white; cursor: pointer;">
                    <p style="margin: 6px 0 0 0; font-size: 0.8rem; color: #64748b;">Optional. Upload a clear photo or PDF copy of your driver's license. Max size 5MB.</p>
                </div>
                <h4 style="font-size: 1.25rem; font-weight: 700; color: var(--primary); margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; margin-top: 40px;">Vehicle Reviews</h4>
                <div id="bookingReviewsPanel" data-vehicle-id="{{ (int) $vehicle->id }}" style="margin-top: -10px; margin-bottom: 30px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap: 12px; flex-wrap: wrap;">
                        <div>
                            <div style="margin-top: 6px; color:#64748b; font-weight: 800; font-size: 0.9rem;">
                                Owner rating:
                                @if(($ownerTotalReviews ?? 0) > 0)
                                    <span style="font-weight: 900; color: var(--accent);">★{{ number_format((float)($ownerAvgRating ?? 0), 1) }}</span>
                                    <span style="font-weight: 900;">({{ (int)($ownerTotalReviews ?? 0) }})</span>
                                @else
                                    <span style="font-weight: 500; color:#94a3b8;">No reviews yet</span>
                                @endif
                            </div>
                        </div>
                        <div style="display:flex; gap: 10px; align-items:center; flex-wrap: wrap;">
                            <span id="vehicleAvgBadge" style="display:inline-flex; align-items:center; gap:8px; padding: 6px 12px; border-radius: 999px; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.25); color: #b45309; font-weight: 900; font-size: 0.85rem;">
                                Vehicle Average Rating ★<span id="vehicleAvgValue">—</span>
                            </span>
                            <span id="vehicleReviewsCountBadge" style="display:inline-flex; align-items:center; gap:8px; padding: 6px 12px; border-radius: 999px; background: rgba(15, 23, 42, 0.06); border: 1px solid rgba(15, 23, 42, 0.12); color: #0f172a; font-weight: 900; font-size: 0.85rem;">
                                <span id="vehicleReviewsCount">—</span> reviews
                            </span>
                        </div>
                    </div>

                    <div id="bookingReviewsState" style="margin-top: 12px; color:#64748b; font-weight: 900;">Loading reviews…</div>
                    <div id="bookingReviewsList" style="margin-top: 12px; display:none; flex-direction: column; gap: 10px;"></div>

                    <div style="margin-top: 12px; display:flex; justify-content:space-between; align-items:center; gap: 10px; flex-wrap: wrap;">
                        <div id="bookingReviewsPageInfo" style="color:#64748b; font-weight: 900; font-size: 0.9rem; display:none;"></div>
                        <div style="display:flex; gap: 10px; justify-content:flex-end;">
                            <button type="button" id="bookingReviewsPrev" class="btn btn-outline" style="padding: 10px 14px; font-size: 0.95rem; display:none;">Previous</button>
                            <button type="button" id="bookingReviewsNext" class="btn btn-outline" style="padding: 10px 14px; font-size: 0.95rem; display:none;">Next</button>
                        </div>
                    </div>
                </div>
                @if(isset($faqs) && $faqs->count() > 0)
                <h4 style="font-size: 1.25rem; font-weight: 700; color: var(--primary); margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; margin-top: 40px;">Frequently Asked Questions</h4>
                    <div id="bookingFaqPanel" style="margin-top: -10px; margin-bottom: 30px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap: 12px; flex-wrap: wrap;">
                            <div>
                                <div style="margin-top: 6px; color:#64748b; font-weight: 800; font-size: 0.9rem;">Tap a question to expand.</div>
                            </div>
                        </div>

                        <div style="margin-top: 12px; display:flex; flex-direction: column; gap: 10px;">
                            @foreach($faqs as $idx => $faq)
                                <div class="aar-faq-item" style="border: 1px solid #e2e8f0; border-radius: 12px; overflow:hidden;">
                                    <button type="button" class="aar-faq-btn" aria-expanded="false" style="width:100%; text-align:left; background: #f8fafc; border:none; padding: 12px 14px; display:flex; justify-content:space-between; align-items:center; gap: 12px; cursor:pointer;">
                                        <span style="font-weight: 900; color:#0f172a;">{{ $faq->question }}</span>
                                        <span class="aar-faq-icon" style="width: 34px; height: 34px; border-radius: 999px; display:flex; align-items:center; justify-content:center; border: 1px solid rgba(245,158,11,0.28); background: rgba(245,158,11,0.14); color: var(--accent); font-weight: 900;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                                        </span>
                                    </button>
                                    <div class="aar-faq-panel" style="display:none; padding: 12px 14px; background: white; color:#0f172a; font-weight: 300; line-height: 1.55; white-space: pre-wrap;">{{ $faq->answer }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                <h4 style="font-size: 1.25rem; font-weight: 700; color: var(--primary); margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; margin-top: 40px;">Message to Vehicle Owner</h4>
                <div style="margin-top: -15px; margin-bottom: 30px;">
                    <label for="additional_message" style="display:block; font-weight:700; color:#0f172a; margin-bottom:6px;">Additional Message to Vehicle Owner / Offers / Discount /Destination Arrangement</label>
                    <textarea id="additional_message" name="additional_message" placeholder="Write a message to the owner (e.g., special requests, offers, discount notes, destination arrangement)" rows="4" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem;">{{ old('additional_message') }}</textarea>
                </div>

                <div style="display: flex; justify-content: space-between; gap: 15px; margin-top: 0px; border-top: 1px solid #e2e8f0; padding-top: 30px; align-items: center; flex-wrap: wrap;">
                    <div style="display:flex; align-items:flex-start; gap: 10px; max-width: 520px;">
                        <input id="agree_terms" name="agree_terms" type="checkbox" value="1" {{ old('agree_terms') ? 'checked' : '' }} style="margin-top: 4px; width: 18px; height: 18px; accent-color: var(--accent);">
                        <div style="min-width: 0;">
                            <label for="agree_terms" style="font-weight: 900; color: var(--primary); cursor: pointer;">
                                I agree to the Terms & Privacy Policy
                            </label>
                            <div style="margin-top: 6px; color:#64748b; font-weight: 700; font-size: 0.9rem; line-height: 1.35;">
                                By submitting, you consent to identity verification and processing of your personal data for booking and compliance purposes.
                                <button type="button" onclick="openBookingTermsModal()" style="background:none; border:none; padding:0; margin-left: 6px; color: var(--accent); font-weight: 900; cursor:pointer; text-decoration: underline; text-decoration-thickness: 2px; text-underline-offset: 2px;">
                                    Read terms
                                </button>
                            </div>
                            @error('agree_terms')
                                <div style="margin-top: 6px; color:#b91c1c; font-weight: 800; font-size: 0.9rem;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div style="display:flex; justify-content:flex-end; gap: 15px; flex-wrap: wrap;">
                        <a href="{{ route('vehicles.index') }}" class="btn" style="background: white; border: 2px solid #e2e8f0; color: #64748b; padding: 14px 35px; text-decoration: none; font-weight: 700;">Cancel Booking</a>
                        <button id="confirmBookingBtn" type="submit" class="btn btn-primary" style="padding: 14px 40px; font-size: 1.1rem; font-weight: 700;" {{ old('agree_terms') ? '' : 'disabled' }}>Confirm Booking</button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <div id="bookingTermsModal" style="display:none; position: fixed; inset: 0; background: rgba(2,6,23,0.88); z-index: 10060; align-items: center; justify-content: center; padding: 20px;">
        <div onclick="closeBookingTermsModal()" style="position:absolute; inset:0;"></div>
        <div style="position: relative; z-index: 1; width: 100%; max-width: 980px; background: white; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.35); max-height: 85vh; display:flex; flex-direction:column;">
            <div style="padding: 14px 16px; background: #0f172a; color: white; display:flex; justify-content:space-between; gap: 10px; align-items:center;">
                <div style="font-weight: 900; letter-spacing: 0.2px;">Terms & Privacy Consent</div>
                <button type="button" onclick="closeBookingTermsModal()" style="background:none; border:none; color:white; font-size: 2rem; line-height: 1; cursor:pointer; opacity:0.85;">&times;</button>
            </div>
            <div style="padding: 16px; background: #f8fafc; overflow:auto;">
                <div style="background:white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px;">
                    <div style="font-weight: 900; color: var(--primary); font-size: 1.05rem;">Summary</div>
                    <ul style="margin-top: 10px; padding-left: 18px; color:#0f172a; font-weight: 500; line-height: 1.45;">
                        <li>We collect only the information needed to process your booking, verify identity, prevent fraud, and comply with the law.</li>
                        <li>IDs and driver’s license details may be verified for authenticity and eligibility.</li>
                        <li>Payment proofs are used strictly to validate transactions and confirm reservations.</li>
                        <li>Your information is protected using appropriate safeguards and is not shared except when required by law.</li>
                    </ul>
                </div>

                <div style="margin-top: 14px; background:white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; white-space: pre-wrap; color:#0f172a; font-weight: 500; line-height: 1.55;">
I hereby voluntarily give my full consent to the Car Rental Service Provider to collect, process, store, and use my personal information for legitimate business and transactional purposes related to my car rental booking.

This includes my full name, address, contact details, valid government-issued identification, driver’s license information, and any supporting documents such as payment transaction receipts or screenshots. I understand that these personal data are required for identity verification, booking processing, fraud prevention, vehicle handover approval, and compliance with applicable laws and regulations.

I acknowledge that my submitted driver’s license and identification documents may be verified to confirm authenticity and eligibility to rent and operate a vehicle. I also understand that payment proofs I provide will be used solely for validating transactions and confirming reservations. Any falsified, altered, or misleading information may result in booking rejection, cancellation, or further legal action if necessary.

I agree that my personal data will be securely stored and protected using appropriate administrative, technical, and organizational safeguards to prevent unauthorized access, disclosure, alteration, or destruction. My information will be treated with strict confidentiality and will not be disclosed to any third party without my consent, except where required or permitted by law, legal processes, or government authorities.

I understand that my personal data may be retained for as long as necessary to fulfill the purposes stated herein, including transaction processing, dispute resolution, record-keeping, and compliance with legal obligations. After the retention period, my data will be securely deleted or anonymized in accordance with data protection standards.

I acknowledge that I have rights under applicable data protection laws, including the right to access, correct, update, or request deletion of my personal data, subject to legal and contractual limitations.

By checking the box and submitting this form, I confirm that I have read, understood, and voluntarily agree to this consent for the collection and processing of my personal data as described above and in the Privacy Policy.
                </div>
            </div>
        </div>
    </div>

    <div id="bookingImagesModal" style="display:none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.85); z-index: 10050; align-items: center; justify-content: center; padding: 20px;">
        <div onclick="closeBookingImagesModal()" style="position:absolute; inset:0;"></div>
        <div style="position: relative; width: 100%; max-width: 900px; background: #0b1220; border: 1px solid #1e293b; border-radius: 12px; overflow: hidden; box-shadow: 0 18px 35px rgba(0,0,0,0.25); z-index: 1;">
            <div style="display:flex; justify-content: space-between; align-items:center; padding: 12px 16px; background: rgba(2, 6, 23, 0.65); border-bottom: 1px solid #1e293b;">
                <div style="color: white; font-weight: 800;">Vehicle Images</div>
                <button type="button" onclick="closeBookingImagesModal()" style="background:none; border:none; color:white; font-size: 2rem; line-height: 1; cursor:pointer; opacity:0.85;">&times;</button>
            </div>
            <div style="position: relative; width: 100%; height: min(70vh, 520px); background: #0b1220; display:flex; align-items:center; justify-content:center; padding: 10px;">
                <button type="button" onclick="prevBookingImage()" id="bookingPrevBtn" style="position:absolute; left: 16px; top: 50%; transform: translateY(-50%); background: rgba(255, 255, 255, 0.1); color: white; border: 1px solid rgba(255, 255, 255, 0.2); width: 44px; height: 44px; border-radius: 999px; font-size: 1.5rem; cursor: pointer;">&lsaquo;</button>
                <img id="bookingModalImage" src="" alt="Vehicle image" style="max-width: 100%; max-height: 100%; object-fit: contain; display:none;">
                <div id="bookingModalFallback" style="display:none; color:#94a3b8; font-weight:700; text-align:center; padding: 40px;">No images available for this vehicle.</div>
                <button type="button" onclick="nextBookingImage()" id="bookingNextBtn" style="position:absolute; right: 16px; top: 50%; transform: translateY(-50%); background: rgba(255, 255, 255, 0.1); color: white; border: 1px solid rgba(255, 255, 255, 0.2); width: 44px; height: 44px; border-radius: 999px; font-size: 1.5rem; cursor: pointer;">&rsaquo;</button>
            </div>
            <div id="bookingThumbs" style="display:flex; gap: 10px; justify-content: center; overflow-x: auto; padding: 12px; border-top: 1px solid #1e293b; background: rgba(2, 6, 23, 0.55);"></div>
        </div>
    </div>

    <script>
        const municipalityData = @json($municipalities);
        const municipalityTypePrices = @json($typePriceMap ?? []);
        const carwashFeePerType = {{ number_format($vehicle->libType->carwash_fee ?? 0, 2, '.', '') }};
        const bookingImageSet = @json($vehicle->images->map(fn($img) => Storage::url($img->image_path))->values());
        let bookingImageIndex = 0;

        function openBookingTermsModal() {
            const m = document.getElementById('bookingTermsModal');
            if (!m) return;
            m.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        function closeBookingTermsModal() {
            const m = document.getElementById('bookingTermsModal');
            if (!m) return;
            m.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const cb = document.getElementById('agree_terms');
            const btn = document.getElementById('confirmBookingBtn');
            if (!cb || !btn) return;
            const sync = () => {
                btn.disabled = !cb.checked;
                btn.style.opacity = cb.checked ? '1' : '0.55';
                btn.style.cursor = cb.checked ? 'pointer' : 'not-allowed';
            };
            cb.addEventListener('change', sync);
            sync();
        });

        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('bookingForm');
            if (!form) return;
            form.addEventListener('submit', () => {
                if (window.AARLoading) window.AARLoading.show('Submitting booking…', 'Saving booking and sending email notification…');
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const items = document.querySelectorAll('#bookingFaqPanel .aar-faq-item');
            if (!items || items.length === 0) return;
            items.forEach((item) => {
                const btn = item.querySelector('.aar-faq-btn');
                const panel = item.querySelector('.aar-faq-panel');
                const icon = item.querySelector('.aar-faq-icon');
                if (!btn || !panel) return;
                btn.addEventListener('click', () => {
                    const isOpen = btn.getAttribute('aria-expanded') === 'true';
                    items.forEach((other) => {
                        const ob = other.querySelector('.aar-faq-btn');
                        const op = other.querySelector('.aar-faq-panel');
                        const oi = other.querySelector('.aar-faq-icon');
                        if (!ob || !op) return;
                        ob.setAttribute('aria-expanded', 'false');
                        op.style.display = 'none';
                        if (oi) oi.style.transform = 'rotate(0deg)';
                    });
                    if (!isOpen) {
                        btn.setAttribute('aria-expanded', 'true');
                        panel.style.display = 'block';
                        if (icon) icon.style.transform = 'rotate(180deg)';
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const widget = document.getElementById('bookingCalendarWidget');
            const grid = document.getElementById('calGrid');
            const monthLabel = document.getElementById('calMonthLabel');
            const prevBtn = document.getElementById('calPrevBtn');
            const nextBtn = document.getElementById('calNextBtn');
            const err = document.getElementById('bookingCalendarError');
            const fromInput = document.getElementById('datetime_from');
            const toInput = document.getElementById('datetime_to');
            if (!widget || !grid || !monthLabel || !prevBtn || !nextBtn || !fromInput || !toInput) return;

            const raw = @json($vehicle->booked_dates ?? []);
            const booked = new Set();

            const pad = (n) => String(n).padStart(2, '0');
            const toDateStr = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

            const parseDateStr = (s) => {
                if (!s || typeof s !== 'string') return null;
                const m = s.match(/^(\d{4})-(\d{2})-(\d{2})/);
                if (!m) return null;
                const dt = new Date(Number(m[1]), Number(m[2]) - 1, Number(m[3]));
                if (Number.isNaN(dt.getTime())) return null;
                return dt;
            };

            const expand = (items) => {
                if (!Array.isArray(items)) return;
                items.forEach((it) => {
                    if (typeof it === 'string') {
                        const d = parseDateStr(it);
                        if (d) booked.add(toDateStr(d));
                        return;
                    }
                    if (it && typeof it === 'object' && it.start && it.end) {
                        const s = parseDateStr(String(it.start));
                        const e = parseDateStr(String(it.end));
                        if (!s || !e) return;
                        const cur = new Date(s.getFullYear(), s.getMonth(), s.getDate());
                        const end = new Date(e.getFullYear(), e.getMonth(), e.getDate());
                        while (cur <= end) {
                            booked.add(toDateStr(cur));
                            cur.setDate(cur.getDate() + 1);
                        }
                    }
                });
            };
            expand(raw);

            const setError = (msg) => {
                if (!err) return;
                if (!msg) { err.style.display = 'none'; err.textContent = ''; return; }
                err.textContent = msg;
                err.style.display = 'block';
            };

            const setInputDate = (input, dateStr, defaultTime) => {
                const v = input.value || '';
                const time = v.includes('T') ? v.split('T')[1] : defaultTime;
                input.value = `${dateStr}T${time}`;
            };

            const getSelectedDateStr = (input) => {
                const v = input.value || '';
                if (!v) return '';
                const p = v.split('T')[0];
                return p || '';
            };

            const today = new Date();
            const todayStr = toDateStr(today);
            let viewYear = today.getFullYear();
            let viewMonth = today.getMonth();

            let startSel = '';
            let endSel = '';

            const syncFromInputs = () => {
                const s = getSelectedDateStr(fromInput);
                const e = getSelectedDateStr(toInput);
                startSel = s || '';
                endSel = e || '';
            };
            syncFromInputs();

            const isBetween = (d, a, b) => {
                if (!a || !b) return false;
                return d >= a && d <= b;
            };

            const render = () => {
                setError('');
                grid.innerHTML = '';

                const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                monthLabel.textContent = `${monthNames[viewMonth]} ${viewYear}`;

                const weekday = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
                weekday.forEach((w) => {
                    const h = document.createElement('div');
                    h.textContent = w;
                    h.style.fontWeight = '900';
                    h.style.color = '#64748b';
                    h.style.textAlign = 'center';
                    h.style.padding = '8px 0';
                    grid.appendChild(h);
                });

                const first = new Date(viewYear, viewMonth, 1);
                const last = new Date(viewYear, viewMonth + 1, 0);
                const startDow = first.getDay();
                const totalDays = last.getDate();

                for (let i = 0; i < startDow; i++) {
                    const empty = document.createElement('div');
                    empty.style.height = '44px';
                    grid.appendChild(empty);
                }

                const a = startSel && endSel && startSel <= endSel ? startSel : (endSel && startSel && endSel < startSel ? endSel : startSel);
                const b = startSel && endSel && startSel <= endSel ? endSel : (endSel && startSel && endSel < startSel ? startSel : endSel);

                for (let day = 1; day <= totalDays; day++) {
                    const dateObj = new Date(viewYear, viewMonth, day);
                    const ds = toDateStr(dateObj);

                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.textContent = String(day);
                    btn.style.height = '44px';
                    btn.style.borderRadius = '10px';
                    btn.style.border = '1px solid #e2e8f0';
                    btn.style.fontWeight = '900';
                    btn.style.cursor = 'pointer';

                    const isPast = ds < todayStr;
                    const isBooked = booked.has(ds);
                    const isSelected = ds === startSel || ds === endSel;
                    const isInRange = a && b ? isBetween(ds, a, b) : false;

                    if (isPast || isBooked) {
                        btn.disabled = true;
                        btn.style.background = isBooked ? '#fee2e2' : '#f1f5f9';
                        btn.style.borderColor = isBooked ? '#fecaca' : '#e2e8f0';
                        btn.style.color = isBooked ? '#b91c1c' : '#94a3b8';
                        btn.style.cursor = 'not-allowed';
                    } else if (isSelected) {
                        btn.style.background = 'rgba(245,158,11,0.85)';
                        btn.style.borderColor = 'rgba(245,158,11,0.9)';
                        btn.style.color = '#0f172a';
                    } else if (isInRange) {
                        btn.style.background = 'rgba(245,158,11,0.18)';
                        btn.style.borderColor = 'rgba(245,158,11,0.25)';
                        btn.style.color = '#0f172a';
                    } else {
                        btn.style.background = '#ecfdf5';
                        btn.style.borderColor = '#bbf7d0';
                        btn.style.color = '#065f46';
                    }

                    btn.addEventListener('click', () => {
                        setError('');
                        if (!startSel || (startSel && endSel)) {
                            startSel = ds;
                            endSel = '';
                        } else {
                            if (ds < startSel) {
                                endSel = startSel;
                                startSel = ds;
                            } else {
                                endSel = ds;
                            }
                        }

                        setInputDate(fromInput, startSel, '09:00');
                        setInputDate(toInput, endSel || startSel, '18:00');
                        calculatePrice();
                        render();
                    });

                    grid.appendChild(btn);
                }
            };

            const validateInputs = () => {
                setError('');
                const s = getSelectedDateStr(fromInput);
                const e = getSelectedDateStr(toInput);
                if (!s || !e) return true;
                if (e < s) {
                    setError('Return date must be after pickup date.');
                    return false;
                }
                const cur = parseDateStr(s);
                const end = parseDateStr(e);
                if (!cur || !end) return true;
                const c = new Date(cur.getFullYear(), cur.getMonth(), cur.getDate());
                const ed = new Date(end.getFullYear(), end.getMonth(), end.getDate());
                while (c <= ed) {
                    const ds = toDateStr(c);
                    if (booked.has(ds)) {
                        setError('Your selected range includes booked dates. Please choose other dates.');
                        return false;
                    }
                    c.setDate(c.getDate() + 1);
                }
                return true;
            };

            const bookingForm = document.getElementById('bookingForm');
            if (bookingForm) {
                bookingForm.addEventListener('submit', (e) => {
                    if (!validateInputs()) {
                        e.preventDefault();
                        if (window.AARLoading) window.AARLoading.hide();
                    }
                });
            }

            fromInput.addEventListener('change', () => {
                syncFromInputs();
                validateInputs();
                render();
            });
            toInput.addEventListener('change', () => {
                syncFromInputs();
                validateInputs();
                render();
            });

            prevBtn.addEventListener('click', () => {
                viewMonth -= 1;
                if (viewMonth < 0) { viewMonth = 11; viewYear -= 1; }
                render();
            });
            nextBtn.addEventListener('click', () => {
                viewMonth += 1;
                if (viewMonth > 11) { viewMonth = 0; viewYear += 1; }
                render();
            });

            render();
        });
        
        function updateProvinces() {
            const regionSelect = document.getElementById('region');
            const provinceSelect = document.getElementById('province');
            const municipalitySelect = document.getElementById('municipality');
            
            provinceSelect.innerHTML = '<option value="">Select Province</option>';
            municipalitySelect.innerHTML = '<option value="">Select Municipality</option>';
            municipalitySelect.disabled = true;
            
            if (regionSelect.value) {
                const provinces = Object.keys(municipalityData[regionSelect.value]);
                provinces.forEach(province => {
                    const option = document.createElement('option');
                    option.value = province;
                    option.textContent = province;
                    provinceSelect.appendChild(option);
                });
                provinceSelect.disabled = false;
            } else {
                provinceSelect.disabled = true;
            }
            calculatePrice();
        }

        function updateMunicipalities() {
            const regionSelect = document.getElementById('region');
            const provinceSelect = document.getElementById('province');
            const municipalitySelect = document.getElementById('municipality');
            
            municipalitySelect.innerHTML = '<option value="">Select Municipality</option>';
            
            if (provinceSelect.value) {
                const municipalities = municipalityData[regionSelect.value][provinceSelect.value];
                municipalities.forEach(m => {
                    const option = document.createElement('option');
                    option.value = m.municipality;
                    if (municipalityTypePrices && municipalityTypePrices[m.id]) {
                        option.textContent = m.municipality;
                        option.dataset.price = municipalityTypePrices[m.id];
                    } else {
                        option.textContent = m.municipality + ' (No rate set)';
                        option.dataset.price = 0;
                        option.disabled = true;
                    }
                    municipalitySelect.appendChild(option);
                });
                municipalitySelect.disabled = false;
            } else {
                municipalitySelect.disabled = true;
            }
            calculatePrice();
        }

        function calculatePrice() {
            const startStr = document.getElementById('datetime_from').value;
            const endStr = document.getElementById('datetime_to').value;
            const municipalitySelect = document.getElementById('municipality');
            const hasCarwash = true;
            
            let days = 0;
            let extraHours = 0;
            let destinationPrice = 0;
            let carwashFee = hasCarwash ? carwashFeePerType : 0;
            let extraHoursFee = 0;
            let baseSurcharge = 0;

            // 1. Duration (Used for text display only, not price)
            if(startStr && endStr) {
                const start = new Date(startStr);
                const end = new Date(endStr);
                
                if (end > start) {
                    const diffTime = Math.abs(end - start);
                    const totalHours = Math.floor(diffTime / (1000 * 60 * 60));
                    
                    days = Math.floor(totalHours / 24);
                    extraHours = totalHours % 24;

                    if (days === 0 && extraHours > 0) {
                        days = 1;
                        extraHours = 0;
                    }
                    
                    let durationText = days + (days === 1 ? " Day" : " Days");
                    if (extraHours > 0) {
                        durationText += ` and ${extraHours} ${extraHours === 1 ? 'Hour' : 'Hours'}`;
                    }
                    
                    document.getElementById('duration_display').innerText = durationText;
                    document.getElementById('duration_display').style.color = "var(--primary)";
                } else {
                    document.getElementById('duration_display').innerText = "Invalid Dates";
                    document.getElementById('duration_display').style.color = "#ef4444";
                }
            }

            // 2. Destination Price (Multiplied by days)
            if (municipalitySelect.value) {
                const selectedOption = municipalitySelect.options[municipalitySelect.selectedIndex];
                baseSurcharge = parseFloat(selectedOption.dataset.price) || 0;
                destinationPrice = baseSurcharge * (days > 0 ? days : 1);
                document.getElementById('summary_municipality').innerText = `${municipalitySelect.value} (₱${baseSurcharge.toLocaleString('en-US', {minimumFractionDigits: 2})} x ${days > 0 ? days : 1} days)`;
            } else {
                document.getElementById('summary_municipality').innerText = "None";
            }

            // 3. Extra Hours
            if (extraHours > 0) {
                document.getElementById('extra_hours_row').style.display = 'flex';
                document.getElementById('summary_extra_hours').innerText = extraHours;
                if (extraHours > 5) {
                    extraHoursFee = baseSurcharge;
                    document.getElementById('summary_extra_hours_fee').innerText = extraHoursFee.toLocaleString('en-US', {minimumFractionDigits: 2}) + " (Full Day)";
                } else {
                    extraHoursFee = extraHours * 200;
                    document.getElementById('summary_extra_hours_fee').innerText = extraHoursFee.toLocaleString('en-US', {minimumFractionDigits: 2});
                }
            } else {
                document.getElementById('extra_hours_row').style.display = 'none';
            }

            // 4. Carwash
            document.getElementById('carwash_row').style.display = hasCarwash ? 'flex' : 'none';
            document.getElementById('summary_carwash_fee').innerText = carwashFee.toLocaleString('en-US', {minimumFractionDigits: 2});

            // 5. Grand Total (Excluding Base Price)
            const grandTotal = destinationPrice + carwashFee + extraHoursFee;

            // Update Summary Display
            document.getElementById('summary_destination_price').innerText = destinationPrice.toLocaleString('en-US', {minimumFractionDigits: 2});
            document.getElementById('computed_price').innerText = grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2});
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            let now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            let currentStr = now.toISOString().slice(0,16);
            document.getElementById('datetime_from').min = currentStr;
            document.getElementById('datetime_to').min = currentStr;
            calculatePrice();
        });

        function openBookingImagesModal() {
            const modal = document.getElementById('bookingImagesModal');
            const img = document.getElementById('bookingModalImage');
            const fallback = document.getElementById('bookingModalFallback');
            const prevBtn = document.getElementById('bookingPrevBtn');
            const nextBtn = document.getElementById('bookingNextBtn');
            const thumbs = document.getElementById('bookingThumbs');

            if (!modal || !img || !fallback || !prevBtn || !nextBtn || !thumbs) return;

            bookingImageIndex = 0;
            thumbs.innerHTML = '';

            if (!bookingImageSet || bookingImageSet.length === 0) {
                img.style.display = 'none';
                fallback.style.display = 'block';
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
            } else {
                fallback.style.display = 'none';
                img.style.display = 'block';
                prevBtn.style.display = bookingImageSet.length > 1 ? 'block' : 'none';
                nextBtn.style.display = bookingImageSet.length > 1 ? 'block' : 'none';
                bookingImageSet.forEach((url, idx) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.style.width = '72px';
                    btn.style.height = '52px';
                    btn.style.borderRadius = '8px';
                    btn.style.overflow = 'hidden';
                    btn.style.border = '2px solid transparent';
                    btn.style.cursor = 'pointer';
                    btn.style.background = 'transparent';
                    btn.onclick = () => { bookingImageIndex = idx; updateBookingModalImage(); };
                    const thumbImg = document.createElement('img');
                    thumbImg.src = url;
                    thumbImg.alt = 'Thumbnail';
                    thumbImg.style.width = '100%';
                    thumbImg.style.height = '100%';
                    thumbImg.style.objectFit = 'cover';
                    btn.appendChild(thumbImg);
                    thumbs.appendChild(btn);
                });
                updateBookingModalImage();
            }

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeBookingImagesModal() {
            const modal = document.getElementById('bookingImagesModal');
            if (!modal) return;
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function updateBookingModalImage() {
            const img = document.getElementById('bookingModalImage');
            const thumbs = document.getElementById('bookingThumbs');
            if (!img || !bookingImageSet || bookingImageSet.length === 0) return;
            img.src = bookingImageSet[bookingImageIndex];
            Array.from(thumbs.children).forEach((btn, idx) => {
                btn.style.borderColor = idx === bookingImageIndex ? 'var(--accent)' : 'transparent';
            });
        }

        function prevBookingImage() {
            if (!bookingImageSet || bookingImageSet.length === 0) return;
            bookingImageIndex = (bookingImageIndex - 1 + bookingImageSet.length) % bookingImageSet.length;
            updateBookingModalImage();
        }

        function nextBookingImage() {
            if (!bookingImageSet || bookingImageSet.length === 0) return;
            bookingImageIndex = (bookingImageIndex + 1) % bookingImageSet.length;
            updateBookingModalImage();
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const panel = document.getElementById('bookingReviewsPanel');
            if (!panel) return;
            const vehicleId = panel.getAttribute('data-vehicle-id');
            if (!vehicleId) return;

            const stateEl = document.getElementById('bookingReviewsState');
            const listEl = document.getElementById('bookingReviewsList');
            const prevBtn = document.getElementById('bookingReviewsPrev');
            const nextBtn = document.getElementById('bookingReviewsNext');
            const pageInfo = document.getElementById('bookingReviewsPageInfo');
            const avgEl = document.getElementById('vehicleAvgValue');
            const countEl = document.getElementById('vehicleReviewsCount');

            const fmtInt = (n) => {
                if (typeof n === 'number' && Number.isFinite(n)) return n.toLocaleString();
                const v = Number(n);
                if (Number.isFinite(v)) return v.toLocaleString();
                return '0';
            };

            let nextUrl = null;
            let prevUrl = null;
            let currentPage = 1;
            let lastPage = 1;

            const renderStars = (rating) => {
                const r = Number(rating || 0);
                let out = '';
                for (let i = 1; i <= 5; i++) {
                    out += '<span style="color:' + (i <= r ? 'var(--accent)' : '#cbd5e1') + '; font-weight: 900;">★</span>';
                }
                return out;
            };

            const renderReviewRow = (review) => {
                const name = review?.reviewer?.name || 'User';
                const date = review?.created_at ? new Date(review.created_at).toLocaleDateString() : '';
                const comment = (review?.comment || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                const rating = Number(review?.rating || 0);
                return (
                    '<div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px;">' +
                        '<div style="display:flex; justify-content:space-between; gap: 10px; flex-wrap: wrap; align-items:flex-start;">' +
                            '<div>' +
                                '<div style="font-weight: 500; color:#0f172a;">' + name + '</div>' +
                                '<div style="margin-top: 4px; display:flex; gap: 4px; align-items:center;">' + renderStars(rating) +
                                    '<span style="margin-left: 8px; color:#94a3b8; font-weight: 900; font-size: 0.85rem;">' + date + '</span>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div style="margin-top: 10px; color:#0f172a; font-weight: 400; font-size: 0.9rem; white-space: pre-wrap;">' + comment + '</div>' +
                    '</div>'
                );
            };

            const load = async (url, append) => {
                if (stateEl) {
                    stateEl.style.display = 'block';
                    stateEl.textContent = append ? 'Loading more…' : 'Loading reviews…';
                }
                if (prevBtn) prevBtn.style.display = 'none';
                if (nextBtn) nextBtn.style.display = 'none';
                if (pageInfo) pageInfo.style.display = 'none';

                try {
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) throw new Error('Unable to load reviews.');
                    const data = await res.json();

                    nextUrl = data.next_page_url || null;
                    prevUrl = data.prev_page_url || null;
                    currentPage = Number(data.current_page || 1);
                    lastPage = Number(data.last_page || 1);
                    if (avgEl) avgEl.textContent = Number(data.avg_rating || 0).toFixed(1);
                    if (countEl) countEl.textContent = fmtInt(Number(data.total_reviews || 0));

                    if (listEl) {
                        if (!append) listEl.innerHTML = '';
                        const items = Array.isArray(data.reviews) ? data.reviews : [];
                        if (items.length === 0 && !append) {
                            if (stateEl) stateEl.textContent = 'No reviews for this vehicle yet.';
                            listEl.style.display = 'none';
                        } else {
                            if (stateEl) stateEl.style.display = 'none';
                            listEl.style.display = 'flex';
                            items.forEach((r) => {
                                listEl.insertAdjacentHTML('beforeend', renderReviewRow(r));
                            });
                        }
                    }

                    if (pageInfo && lastPage > 1) {
                        pageInfo.style.display = 'block';
                        pageInfo.textContent = 'Page ' + fmtInt(currentPage) + ' of ' + fmtInt(lastPage) + ' • ' + fmtInt(Number(data.per_page || 5)) + ' per page';
                    }
                    if (prevBtn) prevBtn.style.display = prevUrl ? 'inline-block' : 'none';
                    if (nextBtn) nextBtn.style.display = nextUrl ? 'inline-block' : 'none';
                } catch (e) {
                    if (stateEl) stateEl.textContent = e?.message || 'Unable to load reviews.';
                }
            };

            prevBtn?.addEventListener('click', () => {
                if (prevUrl) load(prevUrl, false);
            });

            nextBtn?.addEventListener('click', () => {
                if (nextUrl) load(nextUrl, false);
            });

            const firstUrl = '{{ route('reviews.vehicle', ['vehicle' => '__ID__']) }}'.replace('__ID__', vehicleId);
            load(firstUrl, false);
        });
    </script>
</x-member-layout>
