<div style="padding: 18px;">
    <div style="display:flex; align-items:center; justify-content:space-between; gap: 12px; margin-bottom: 14px;">
        <div>
            <div style="font-weight: 900; color: var(--primary); font-size: 1.1rem;">Complete Your Booking</div>
            <div style="color:#64748b; font-weight: 700; margin-top: 4px;">Vehicle: {{ $vehicle->name }}</div>
        </div>
        <div style="font-weight: 900; color: var(--accent);">
            ₱{{ number_format($vehicle->price_per_day, 2) }} / day
        </div>
    </div>

    @php $primaryImage = $vehicle->images->where('is_primary', true)->first() ?? $vehicle->images->first(); @endphp
    <div style="display:flex; align-items:center; gap: 12px; padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; margin-bottom: 14px;">
        <div style="width: 120px; height: 78px; border-radius: 10px; background: #e2e8f0; overflow: hidden; flex-shrink:0;">
            @if($primaryImage)
                <img src="{{ Storage::url($primaryImage->image_path) }}" alt="{{ $vehicle->name }}" style="width:100%; height:100%; object-fit: cover;">
            @else
                <div style="display:flex; height:100%; align-items:center; justify-content:center; color:#64748b; font-weight: 900;">No Image</div>
            @endif
        </div>
        <div style="flex: 1;">
            <div style="font-weight: 900; color:#0f172a;">{{ $vehicle->libBrand->name ?? 'N/A' }} • {{ $vehicle->year_model ?? '—' }}</div>
            <div style="margin-top: 4px; color:#64748b; font-weight: 800; font-size: 0.9rem;">
                Type: {{ $vehicle->libType->name ?? 'N/A' }} • Transmission: {{ $vehicle->libTransmission->name ?? 'N/A' }} • Fuel: {{ $vehicle->libFuelType->name ?? 'N/A' }}
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 820px) {
            .dispatch-grid-3 { grid-template-columns: 1fr !important; }
            .dispatch-grid-2 { grid-template-columns: 1fr !important; }
        }
    </style>

    <form id="dispatchBookingForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

        <div style="margin-bottom: 14px; display:flex; justify-content:flex-end; gap: 10px; flex-wrap:wrap;">
            <button type="button"
                class="btn btn-outline"
                style="padding: 10px 12px;"
                data-aar-calendar-open="1"
                data-title="{{ $vehicle->name }} Calendar"
                data-vehicle-id="{{ (int)$vehicle->id }}"
                data-events-url="{{ route('vehicles.calendar.events', $vehicle) }}"
                data-owner-booking-url=""
                data-can-owner-book="0">
                View Calendar
            </button>
        </div>

        <div class="dispatch-grid-3" style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px;">
            <div>
                <label style="display:block; font-weight: 800; margin-bottom: 6px;">Region</label>
                <select name="region" id="dispatch_region" required style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                    <option value="" selected disabled>Select region</option>
                    @foreach($municipalities as $region => $provinces)
                        <option value="{{ $region }}">{{ $region }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display:block; font-weight: 800; margin-bottom: 6px;">Province</label>
                <select name="province" id="dispatch_province" required style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                    <option value="" selected disabled>Select province</option>
                </select>
            </div>
            <div>
                <label style="display:block; font-weight: 800; margin-bottom: 6px;">Municipality</label>
                <select name="municipality" id="dispatch_municipality" required style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                    <option value="" selected disabled>Select municipality</option>
                </select>
            </div>
        </div>

        <div class="dispatch-grid-2" style="display:grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 14px;">
            <div>
                <label style="display:block; font-weight: 800; margin-bottom: 6px;">Start Date & Time</label>
                <input name="datetime_from" type="datetime-local" required style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
            </div>
            <div>
                <label style="display:block; font-weight: 800; margin-bottom: 6px;">End Date & Time</label>
                <input name="datetime_to" type="datetime-local" required style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
            </div>
        </div>

        <div style="margin-top: 14px;">
            <label style="display:block; font-weight: 800; margin-bottom: 6px;">Pickup Location</label>
            <input name="pickup_location" type="text" required style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
        </div>

        <div style="margin-top: 14px;">
            <label style="display:block; font-weight: 800; margin-bottom: 6px;">Downpayment Amount (Optional)</label>
            <input name="downpayment_amount" type="number" step="0.01" min="0" style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
        </div>

        <div class="dispatch-grid-2" style="display:grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 14px;">
            <div>
                <label style="display:block; font-weight: 800; margin-bottom: 6px;">Downpayment Attachments (Optional)</label>
                <input name="attachments[]" type="file" multiple style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px; background: #f8fafc;">
            </div>
            <div>
                <label style="display:block; font-weight: 800; margin-bottom: 6px;">Driver's License (Optional)</label>
                <input name="drivers_license" type="file" style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px; background: #f8fafc;">
            </div>
        </div>

        <div style="margin-top: 14px;">
            <label style="display:block; font-weight: 800; margin-bottom: 6px;">Additional Message (Optional)</label>
            <textarea name="additional_message" rows="3" style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;"></textarea>
        </div>

        <div style="margin-top: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px;">
            <div style="font-weight: 900; color: var(--primary);">Referral</div>
            <div style="color:#64748b; font-weight: 800; margin-top: 4px;">AARACC Booking</div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap: 10px; margin-top: 18px;">
            <button type="submit" class="btn btn-primary" style="padding: 10px 16px; font-size: 0.95rem;">Confirm Dispatch</button>
        </div>
    </form>
</div>

<script>
    (function () {
        const data = @json($municipalities);
        const $region = document.getElementById('dispatch_region');
        const $province = document.getElementById('dispatch_province');
        const $municipality = document.getElementById('dispatch_municipality');

        function resetSelect(sel, placeholder) {
            while (sel.options.length > 0) sel.remove(0);
            const opt = document.createElement('option');
            opt.value = '';
            opt.disabled = true;
            opt.selected = true;
            opt.textContent = placeholder;
            sel.appendChild(opt);
        }

        function populateSelect(sel, values) {
            values.forEach(v => {
                const opt = document.createElement('option');
                opt.value = v;
                opt.textContent = v;
                sel.appendChild(opt);
            });
        }

        resetSelect($province, 'Select province');
        resetSelect($municipality, 'Select municipality');

        $region.addEventListener('change', function () {
            resetSelect($province, 'Select province');
            resetSelect($municipality, 'Select municipality');
            const provinces = data[this.value] ? Object.keys(data[this.value]) : [];
            populateSelect($province, provinces);
        });

        $province.addEventListener('change', function () {
            resetSelect($municipality, 'Select municipality');
            const region = $region.value;
            const munis = (data[region] && data[region][this.value]) ? data[region][this.value].map(x => x.municipality) : [];
            populateSelect($municipality, munis);
        });
    })();
</script>
