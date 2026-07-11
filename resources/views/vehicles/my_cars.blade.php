<x-member-layout>
    <x-slot name="header">
        <style>
            .vehicle-layout { display: flex; gap: 30px; flex-wrap: wrap; }
            .filter-sidebar { flex: 1; min-width: 250px; max-width: 300px; background: white; padding: 25px; border-radius: 12px; box-shadow: var(--shadow-sm); align-self: flex-start; }
            .vehicle-main { flex: 3; min-width: 300px; }
            .vehicle-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
            .availability-tooltip { position: relative; display: inline-flex; align-items: center; }
            .availability-tooltip:hover::after { content: attr(data-tooltip); position: absolute; bottom: calc(100% + 10px); left: 0; background: #0f172a; color: white; padding: 10px 12px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; white-space: pre-line; min-width: 220px; max-width: 280px; box-shadow: 0 15px 40px rgba(0,0,0,0.25); z-index: 9999; }
            .availability-tooltip:hover::before { content: ''; position: absolute; bottom: calc(100% + 4px); left: 14px; border: 6px solid transparent; border-top-color: #0f172a; z-index: 10000; }
            @media (max-width: 1280px) {
                .vehicle-grid { grid-template-columns: repeat(3, 1fr); }
            }
            @media (max-width: 1024px) {
                .vehicle-grid { grid-template-columns: repeat(2, 1fr); }
                .filter-sidebar { max-width: 100%; flex-basis: 100%; }
            }
            @media (max-width: 768px) {
                .vehicle-grid { grid-template-columns: 1fr; }
            }
        </style>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2>My Vehicles</h2>
                <p style="color: #64748b; margin-top: 5px;">Manage your listed vehicles, update details, and track their availability.</p>
            </div>
            <button onclick="document.getElementById('addVehicleModal').style.display='flex'" class="btn btn-primary">
                + Add Vehicle
            </button>
        </div>
    </x-slot>

    <div class="container" style="padding: 0px 20px;">
        <div class="vehicle-layout">
            <aside class="filter-sidebar">
                <h3 style="margin-bottom: 20px; font-size: 1.3rem; color: var(--primary);">Status</h3>
                <form action="{{ route('my-cars.index') }}" method="GET">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px;">Availability Status</label>
                        <select name="availability_status" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            <option value="">All Status</option>
                            @foreach($statuses as $s)
                                <option value="{{ $s->id }}" {{ (string)request('availability_status') === (string)$s->id ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary" style="flex: 1; padding: 10px;">Apply Filter</button>
                        <a href="{{ route('my-cars.index') }}" class="btn btn-outline" style="padding: 10px; text-align: center;">Clear</a>
                    </div>
                </form>
            </aside>

            <div class="vehicle-main" style="width: 100%;">
                <div class="vehicle-grid">
                    @forelse($vehicles as $vehicle)
                    <div class="vehicle-card" style="background: white; border-radius: 12px; overflow: visible; box-shadow: var(--shadow-sm); transition: var(--transition);">
                        <div onclick='viewImages({{ $vehicle->id }}, @json($vehicle->images->sortByDesc("is_primary")->map(function($img) { return ["id" => $img->id, "url" => Storage::url($img->image_path), "is_primary" => (bool)$img->is_primary]; })->values()))' style="position: relative; height: 180px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; cursor: pointer; border-radius: 12px 12px 0 0; overflow: hidden;" title="Click to view images">
                            @php $primaryImage = $vehicle->images->where('is_primary', true)->first() ?? $vehicle->images->first(); @endphp
                            @if($primaryImage)
                                <img src="{{ Storage::url($primaryImage->image_path) }}" alt="{{ $vehicle->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
                            @endif
                            
                            <span style="position: absolute; top: 10px; right: 10px; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; 
                                @if(strtolower($vehicle->libAvailabilityStatus->name) == 'available') background: #10b981; color: white;
                                @elseif(strtolower($vehicle->libAvailabilityStatus->name) == 'pending') background: #3b82f6; color: white;
                                @elseif(strtolower($vehicle->libAvailabilityStatus->name) == 'rented') background: #ef4444; color: white;
                                @else background: #f59e0b; color: white; @endif">
                                {{ $vehicle->libAvailabilityStatus->name ?? 'Unknown' }}
                            </span>
                        </div>
                        
                        <div style="padding: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                <div>
                                    <h4 style="font-size: .9rem; font-weight: 700; color: var(--primary);">{{ $vehicle->name }} {{ $vehicle->year_model ? '('.$vehicle->year_model.')' : '' }}</h4>
                                    <p style="color: #64748b; font-size: 0.9rem;">{{ $vehicle->libBrand->name ?? 'Unknown' }}</p>
                                </div>
                                <div style="text-align: right;">
                                    <span style="font-size: .75rem; font-weight: 400; color: var(--accent);">Starts at</span> <span style="font-size: .8rem; font-weight: 600; color: var(--accent);"> ₱{{ number_format($vehicle->price_per_day, 2) }}</span>
                                    <div style="font-size: 0.8rem; color: #64748b;">per day</div>
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; font-size: 0.85rem; color: #475569;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 10v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V10M3 10h18M12 2v8M8 6h8"/></svg>
                                    {{ $vehicle->libType->name ?? 'N/A' }}
                                </div>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                                    {{ $vehicle->libTransmission->name ?? 'N/A' }}
                                </div>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 22v-8p2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v8"/><path d="M11 22H3"/><path d="M14 9V4a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v5"/><path d="M14 13h5.5l2 3v3H14v-6z"/></svg>
                                    {{ $vehicle->libFuelType->name ?? 'N/A' }}
                                </div>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"/></svg>
                                    {{ $vehicle->color ?? 'Not stated' }}
                                </div>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M9 9h6v6H9z"/></svg>
                                    {{ $vehicle->displacement ?? 'N/A' }}
                                </div>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    {{ $vehicle->seating_capacity }} Seats
                                </div>
                                @php
                                    $availabilityText = 'Available anytime';
                                    $availabilityTooltip = '';
                                    $ad = $vehicle->booked_dates;
                                    if (is_array($ad) && count($ad) > 0) {
                                        $availabilityText = 'Show booked dates';
                                        $parts = [];
                                        foreach ($ad as $item) {
                                            if (is_string($item)) {
                                                try {
                                                    $parts[] = \Carbon\Carbon::createFromFormat('Y-m-d', $item)->format('F j, Y');
                                                } catch (\Throwable $e) {
                                                }
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
                                                } catch (\Throwable $e) {
                                                }
                                            }
                                        }
                                        $availabilityTooltip = implode("\n", $parts);
                                    }
                                @endphp
                                <div style="display: flex; align-items: center; gap: 2px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 7V3m8 4V3"/><path d="M4 11h16"/><rect x="4" y="5" width="16" height="16" rx="2"/><path d="M8 15h2"/><path d="M14 15h2"/></svg>
                                    @if($availabilityTooltip)
                                        <span class="availability-tooltip" data-tooltip="{{ $availabilityTooltip }}" style="font-weight: 300; color: var(--accent); cursor: help;">{{ $availabilityText }}</span>
                                    @else
                                        <span style="font-weight: 300; color: #0f172a;">{{ $availabilityText }}</span>
                                    @endif
                                </div>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                    {{ $vehicle->images->count() }} Images
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 10px;">
                                <button onclick='editVehicle({{ $vehicle->id }}, "{{ addslashes($vehicle->name) }}", "{{ addslashes($vehicle->license_plate ?? '') }}", {{ $vehicle->lib_brand_id ?? "null" }}, {{ $vehicle->lib_type_id ?? "null" }}, {{ $vehicle->price_per_day }}, {{ $vehicle->lib_availability_status_id ?? "null" }}, {{ $vehicle->lib_transmission_id ?? "null" }}, {{ $vehicle->lib_fuel_type_id ?? "null" }}, {{ $vehicle->seating_capacity }}, "{{ addslashes($vehicle->color ?? '') }}", "{{ addslashes($vehicle->year_model ?? '') }}", @json($vehicle->images->map(fn($img) => ["id" => $img->id, "url" => Storage::url($img->image_path), "is_primary" => (bool)$img->is_primary])))' class="btn btn-outline" style="flex: 1; padding: 8px;">Edit</button>
                                <form action="{{ route('my-cars.destroy', $vehicle->id) }}" method="POST" class="confirm-delete" style="flex: 1; display:flex;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn" style="flex: 1; padding: 8px; background: #ef4444; color: white;">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 50px; background: white; border-radius: 12px;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin: 0 auto 15px;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        <h3 style="color: #64748b; margin-bottom: 10px;">No vehicles found</h3>
                        <p style="color: #94a3b8; font-size: 0.95rem;">Try adjusting your filters or add a new vehicle.</p>
                    </div>
                @endforelse
                </div>
                
                <div style="margin-top: 30px;">
                    {{ $vehicles->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Add Vehicle Modal -->
    <div id="addVehicleModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: white; width: 100%; max-width: 600px; border-radius: 12px; box-shadow: var(--shadow-lg); max-height: 90vh; overflow-y: auto;">
            <div style="padding: 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.5rem; font-weight: 700;">Add New Vehicle</h3>
                <button onclick="document.getElementById('addVehicleModal').style.display='none'" style="background: none; border: none; cursor: pointer; color: #64748b;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            
            <form action="{{ route('my-cars.store') }}" method="POST" enctype="multipart/form-data" style="padding: 20px;">
                @csrf
                <input type="hidden" name="booked_dates" value="[]">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Name</label>
                        <input type="text" name="name" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">License Plate</label>
                        <input type="text" name="license_plate" required maxlength="20" inputmode="text" autocapitalize="characters" spellcheck="false" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;" onkeydown="if(event.key===' '||event.code==='Space'){event.preventDefault();}" oninput="this.value = (this.value || '').replace(/\s+/g, '').toUpperCase();">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Brand</label>
                        <select name="lib_brand_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            <option value="" disabled selected>Select a Brand</option>
                            @foreach($brands as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Type</label>
                        <select name="lib_type_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            <option value="" disabled selected>Select a Type</option>
                            @foreach($types as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Transmission</label>
                        <select name="lib_transmission_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            <option value="" disabled selected>Select a Transmission</option>
                            @foreach($transmissions as $tr)
                                <option value="{{ $tr->id }}">{{ $tr->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Fuel Type</label>
                        <select name="lib_fuel_type_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            <option value="" disabled selected>Select a Fuel Type</option>
                            @foreach($fuels as $f)
                                <option value="{{ $f->id }}">{{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Seating Capacity</label>
                        <input type="number" name="seating_capacity" min="1" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Color</label>
                        <input type="text" name="color" placeholder="e.g. Gloss Black" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Year Model</label>
                        <input type="text" name="year_model" placeholder="e.g. 2024" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Price / Day (₱)</label>
                        <input type="number" step="0.01" name="price_per_day" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 500;">Status</label>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        @foreach($statuses as $k => $s)
                            <label style="cursor: pointer; position: relative;">
                                <input type="radio" name="lib_availability_status_id" value="{{ $s->id }}" {{ $k == 0 ? 'required' : '' }} style="position: absolute; opacity: 0; pointer-events: none;" onchange="this.closest('div').querySelectorAll('.status-oval').forEach(el => { el.style.background = 'transparent'; el.style.color = '#64748b'; el.style.borderColor = '#cbd5e1'; }); if(this.checked) { this.nextElementSibling.style.background = 'var(--accent)'; this.nextElementSibling.style.color = 'white'; this.nextElementSibling.style.borderColor = 'var(--accent)'; }">
                                <div class="status-oval" style="padding: 8px 20px; border-radius: 50px; border: 1px solid #cbd5e1; color: #64748b; font-weight: 600; font-size: 0.9rem; transition: all 0.2s;">
                                    {{ $s->name }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div style="margin-bottom: 0px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Images (Max: 6)</label>
                    <input type="file" id="add_images" name="images[]" accept="image/*" multiple style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: white;" onchange="previewUploads(this, 'addUploadPreview')">
                    <p style="font-size: 0.8rem; color: #64748b; margin-top: 5px;">You can select up to 6 files at once.</p>
                    <input type="hidden" name="primary_upload_index" id="add_primary_upload_index" value="">
                    <div id="addUploadPreview" style="display: flex; gap: 10px; overflow-x: auto; padding-top: 10px; margin-top: 10px;"></div>
                </div>

                <div style="display:flex; justify-content:space-between; gap: 12px; margin-top: 5px; align-items:center; flex-wrap: wrap;">
                    <label style="display:flex; align-items:center; gap:10px; color:#475569; font-weight: 700;">
                        <input id="add_agree_vehicle_terms" type="checkbox" style="width:18px; height:18px; accent-color: var(--accent);">
                        <span>I agree to the <button type="button" onclick="openMyCarsTermsModal()" style="background:none; border:none; padding:0; color: var(--accent); font-weight: 900; cursor:pointer; text-decoration: underline; text-decoration-thickness: 2px; text-underline-offset: 2px;">
                            Terms & Privacy Policy
                        </button></span>

                    </label>
                    <div style="display:flex; justify-content:flex-end; gap: 10px;">
                        <button type="button" onclick="document.getElementById('addVehicleModal').style.display='none'" class="btn btn-outline">Cancel</button>
                        <button id="addVehicleSubmitBtn" type="submit" class="btn btn-primary" disabled style="opacity: 0.55; cursor: not-allowed;">Save Vehicle</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="myCarsTermsModal" style="display:none; position: fixed; inset: 0; background: rgba(2,6,23,0.88); z-index: 12000; align-items: center; justify-content: center; padding: 20px;">
        <div onclick="closeMyCarsTermsModal()" style="position:absolute; inset:0;"></div>
        <div style="position: relative; z-index: 1; width: 100%; max-width: 980px; background: white; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.35); max-height: 85vh; display:flex; flex-direction:column;">
            <div style="padding: 14px 16px; background: #0f172a; color: white; display:flex; justify-content:space-between; gap: 10px; align-items:center;">
                <div style="font-weight: 900; letter-spacing: 0.2px;">Terms & Privacy Policy (Vehicle Listing)</div>
                <button type="button" onclick="closeMyCarsTermsModal()" style="background:none; border:none; color:white; font-size: 2rem; line-height: 1; cursor:pointer; opacity:0.85;">&times;</button>
            </div>
            <div style="padding: 16px; background: #f8fafc; overflow:auto;">
                <div style="background:white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px;">
                    <div style="font-weight: 900; color: var(--primary); font-size: 1.05rem;">Owner Consent & Vehicle Information Policy</div>
                    <div style="margin-top: 10px; color:#0f172a; font-weight: 500; line-height: 1.6; white-space: pre-wrap;">
By adding a vehicle to Auto Amegos Rent-a-Car (“Platform”), you confirm that you are authorized to list the vehicle for rental and that the information you provide is accurate, complete, and truthful.

1) Information Collected for Vehicle Listing
The Platform will collect and process listing-related information such as:
- Vehicle name, brand, type, transmission, fuel type, seating capacity, displacement, color, year model
- License plate number and availability dates/ranges
- Vehicle photos/images and related metadata (e.g., cover photo selection)

2) Purpose of Collection and Processing
We process the listing information to:
- Publish and manage your vehicle listing on the Platform
- Help renters identify the correct vehicle and understand pricing and specifications
- Validate listing quality, prevent fraud, and resolve disputes
- Support booking confirmation, vehicle handover, and customer support workflows
- Comply with applicable laws, regulations, and lawful requests

3) Your Responsibilities and Representations
You agree that:
- You have the legal right/authority to list the vehicle and to rent it out
- The license plate and vehicle details provided correspond to the listed vehicle
- Photos represent the actual condition of the vehicle and are not misleading
- You will keep availability dates, pricing, and details updated

4) Prohibited Content and Misrepresentation
The following are strictly prohibited:
- Uploading falsified/altered details, misleading photos, or stolen images
- Using another person’s license plate number or vehicle identity
- Listing vehicles you do not own or are not authorized to rent
Violation may result in listing removal, account restrictions, booking cancellation, or further action as permitted by law.

5) Privacy, Storage, and Sharing
We protect your data using appropriate safeguards. Your listing information (excluding sensitive personal identifiers) may be shown to renters as part of the vehicle listing. We do not sell your personal data. We may disclose information when required by law, legal processes, or to protect Platform integrity and users’ safety.

6) Retention
Listing information may be retained for as long as needed for platform operations, record-keeping, dispute resolution, and legal compliance. Data may be deleted or anonymized when no longer needed, subject to legal requirements.

7) Your Rights
You may request updates or corrections to your listing data through the Platform tools. You may also request deletion subject to contractual and legal limitations.

By checking the box, you confirm that you have read and agree to these Terms & Privacy Policy for submitting vehicle listing information.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Vehicle Modal -->
    <div id="editVehicleModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: white; width: 100%; max-width: 600px; border-radius: 12px; box-shadow: var(--shadow-lg); max-height: 90vh; overflow-y: auto;">
            <div style="padding: 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.5rem; font-weight: 700;">Edit Vehicle</h3>
                <button onclick="document.getElementById('editVehicleModal').style.display='none'" style="background: none; border: none; cursor: pointer; color: #64748b;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            
            <form id="editForm" method="POST" enctype="multipart/form-data" style="padding: 20px;">
                @csrf
                @method('PUT')
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Name</label>
                        <input type="text" id="edit_name" name="name" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">License Plate</label>
                        <input type="text" id="edit_license_plate" name="license_plate" required maxlength="20" inputmode="text" autocapitalize="characters" spellcheck="false" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;" onkeydown="if(event.key===' '||event.code==='Space'){event.preventDefault();}" oninput="this.value = (this.value || '').replace(/\s+/g, '').toUpperCase();">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Brand</label>
                        <select id="edit_brand" name="lib_brand_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            @foreach($brands as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Type</label>
                        <select id="edit_type" name="lib_type_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            @foreach($types as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Transmission</label>
                        <select id="edit_transmission" name="lib_transmission_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            @foreach($transmissions as $tr)
                                <option value="{{ $tr->id }}">{{ $tr->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Fuel Type</label>
                        <select id="edit_fuel" name="lib_fuel_type_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            @foreach($fuels as $f)
                                <option value="{{ $f->id }}">{{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Seating Capacity</label>
                        <input type="number" id="edit_seating" name="seating_capacity" min="1" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Color</label>
                        <input type="text" id="edit_color" name="color" placeholder="e.g. Gloss Black" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Year Model</label>
                        <input type="text" id="edit_year" name="year_model" placeholder="e.g. 2024" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Price / Day (₱)</label>
                        <input type="number" step="0.01" id="edit_price" name="price_per_day" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 500;">Status</label>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        @foreach($statuses as $k => $s)
                            <label style="cursor: pointer; position: relative;">
                                <input type="radio" name="lib_availability_status_id" value="{{ $s->id }}" {{ $k == 0 ? 'required' : '' }} style="position: absolute; opacity: 0; pointer-events: none;" onchange="this.closest('div').querySelectorAll('.status-oval').forEach(el => { el.style.background = 'transparent'; el.style.color = '#64748b'; el.style.borderColor = '#cbd5e1'; }); if(this.checked) { this.nextElementSibling.style.background = 'var(--accent)'; this.nextElementSibling.style.color = 'white'; this.nextElementSibling.style.borderColor = 'var(--accent)'; }">
                                <div class="status-oval" style="padding: 8px 20px; border-radius: 50px; border: 1px solid #cbd5e1; color: #64748b; font-weight: 600; font-size: 0.9rem; transition: all 0.2s;">
                                    {{ $s->name }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <input type="hidden" name="delete_image_ids" id="edit_delete_image_ids" value="[]">

                <div id="editPrimaryImageContainer" style="display: none; margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Select Primary Cover Photo</label>
                    <div id="editPrimaryImageGrid" style="display: flex; gap: 10px; overflow-x: auto; padding-bottom: 10px;">
                        <!-- Injected via JS -->
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Append New Images (Max: 6)</label>
                    <input type="file" id="edit_images" name="images[]" accept="image/*" multiple style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: white;" onchange="previewUploads(this, 'editUploadPreview')">
                    <p style="font-size: 0.8rem; color: #64748b; margin-top: 5px;">Uploading new images will simply add to the existing collection up to 6 max.</p>
                    <input type="hidden" name="primary_upload_index" id="edit_primary_upload_index" value="">
                    <div id="editUploadPreview" style="display: flex; gap: 10px; overflow-x: auto; padding-top: 10px; margin-top: 10px;"></div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="document.getElementById('editVehicleModal').style.display='none'" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Vehicle</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Images Modal -->
    <div id="viewImagesModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 10000; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #111; width: 100%; max-width: 900px; border-radius: 12px; overflow: hidden; position: relative; display: flex; align-items: center; justify-content: center;">
            <button onclick="closeImagesModal()" style="position: absolute; top: 15px; right: 15px; background: rgba(245, 158, 11, 0.14); border: 1px solid rgba(245, 158, 11, 0.28); cursor: pointer; color: var(--accent); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; z-index: 10; transition: all 0.2s;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            

            <span id="primaryBadge" style="position: absolute; top: 25px; left: 15px; background: rgba(59, 130, 246, 0.9); border: none; color: white; border-radius: 6px; padding: 8px 15px; font-weight: 600; font-size: 0.9rem; z-index: 10; display: none;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline; margin-right: 5px; vertical-align: middle;"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg> Primary Photo
            </span>
            
            <button id="prevImageBtn" onclick="prevImage()" style="position: absolute; left: 15px; background: rgba(245, 158, 11, 0.14); border: 1px solid rgba(245, 158, 11, 0.28); cursor: pointer; color: var(--accent); border-radius: 50%; width: 50px; height: 50px; display: none; align-items: center; justify-content: center; z-index: 10; transition: all 0.2s;">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
            </button>

            <button id="nextImageBtn" onclick="nextImage()" style="position: absolute; right: 15px; background: rgba(245, 158, 11, 0.14); border: 1px solid rgba(245, 158, 11, 0.28); cursor: pointer; color: var(--accent); border-radius: 50%; width: 50px; height: 50px; display: none; align-items: center; justify-content: center; z-index: 10; transition: all 0.2s;">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
            </button>
            
            <div id="imagesContainer" style="display: flex; width: 100%; height: 75vh; align-items: center; justify-content: center;">
                <img id="modalMainImage" src="" style="max-width: 100%; max-height: 100%; object-fit: contain; display: none;">
            </div>
            
            <div id="imagesFallback" style="padding: 60px; text-align: center; color: white; display: none; width: 100%; height: 75vh; flex-direction: column; align-items: center; justify-content: center;">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.5" style="margin-bottom: 15px;"><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/></svg>
                <p>No images available for this vehicle.</p>
            </div>
            
            <div id="imageCounter" style="position: absolute; bottom: 15px; background: rgba(0,0,0,0.6); padding: 5px 15px; border-radius: 20px; color: white; font-size: 0.9rem; display: none;"></div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        let modalImageSet = [];
        let modalImageIndex = 0;
        let modalCurrentVehicleId = null;

        function previewUploads(input, previewContainerId) {
            const container = document.getElementById(previewContainerId);
            const indexInputId = previewContainerId === 'addUploadPreview' ? 'add_primary_upload_index' : 'edit_primary_upload_index';
            const indexInput = document.getElementById(indexInputId);
            
            container.innerHTML = '';
            indexInput.value = '';

            if (input.files && input.files.length > 0) {
                indexInput.value = '0';
                
                Array.from(input.files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.style.minWidth = '80px';
                        div.style.textAlign = 'center';

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '80px';
                        img.style.height = '60px';
                        img.style.objectFit = 'cover';
                        img.style.borderRadius = '6px';
                        img.style.marginBottom = '5px';

                        const radio = document.createElement('input');
                        radio.type = 'radio';
                        radio.name = previewContainerId + '_radio';
                        radio.value = index;
                        radio.title = 'Make primary photo';
                        if (index === 0) radio.checked = true;

                        radio.onchange = function() {
                            indexInput.value = this.value;
                        };

                        div.appendChild(img);
                        div.appendChild(document.createElement('br'));
                        div.appendChild(radio);
                        container.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            }
        }

        function viewImages(id, images) {
            modalCurrentVehicleId = id;
            const fallback = document.getElementById('imagesFallback');
            const mainImg = document.getElementById('modalMainImage');
            const prevBtn = document.getElementById('prevImageBtn');
            const nextBtn = document.getElementById('nextImageBtn');
            const counter = document.getElementById('imageCounter');
            
            modalImageSet = images || [];
            modalImageIndex = 0;
            
            if (modalImageSet.length === 0) {
                mainImg.style.display = 'none';
                fallback.style.display = 'block';
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
                counter.style.display = 'none';
            } else {
                fallback.style.display = 'none';
                mainImg.style.display = 'block';
                updateModalImage();
                
                if (modalImageSet.length > 1) {
                    prevBtn.style.display = 'flex';
                    nextBtn.style.display = 'flex';
                    counter.style.display = 'block';
                } else {
                    prevBtn.style.display = 'none';
                    nextBtn.style.display = 'none';
                    counter.style.display = 'none';
                }
            }
            
            document.getElementById('viewImagesModal').style.display = 'flex';
            document.body.style.overflow = 'hidden'; // stop page scrolling
        }

        async function setAsPrimary() {
            if (modalImageSet.length > 0) {
                const currentImg = modalImageSet[modalImageIndex];
                if(currentImg && currentImg.id) {
                    try {
                        const res = await fetch(`/my-cars/images/${currentImg.id}/primary`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });
                        
                        if(res.ok) {
                            window.location.reload();
                        }
                    } catch(e) {
                        console.error("Error setting primary", e);
                    }
                }
            }
        }

        function updateModalImage() {
            const mainImg = document.getElementById('modalMainImage');
            const counter = document.getElementById('imageCounter');
            const makePrimaryBtn = document.getElementById('makePrimaryBtn');
            const primaryBadge = document.getElementById('primaryBadge');
            
            if (modalImageSet.length > 0) {
                const imgObj = modalImageSet[modalImageIndex];
                mainImg.src = imgObj.url;
                counter.textContent = `${modalImageIndex + 1} / ${modalImageSet.length}`;
                
                if (imgObj.is_primary) {
                    if(makePrimaryBtn) makePrimaryBtn.style.display = 'none';
                    if(primaryBadge) primaryBadge.style.display = 'block';
                } else {
                    if(makePrimaryBtn) makePrimaryBtn.style.display = 'block';
                    if(primaryBadge) primaryBadge.style.display = 'none';
                }
            }
        }

        function prevImage() {
            if (modalImageSet.length > 0) {
                modalImageIndex = (modalImageIndex - 1 + modalImageSet.length) % modalImageSet.length;
                updateModalImage();
            }
        }

        function nextImage() {
            if (modalImageSet.length > 0) {
                modalImageIndex = (modalImageIndex + 1) % modalImageSet.length;
                updateModalImage();
            }
        }

        function closeImagesModal() {
            document.getElementById('viewImagesModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            const makePrimaryBtn = document.getElementById('makePrimaryBtn');
            const primaryBadge = document.getElementById('primaryBadge');
            if(makePrimaryBtn) makePrimaryBtn.style.display = 'none';
            if(primaryBadge) primaryBadge.style.display = 'none';
        }

        const bookedDatesState = { add: [], edit: [] };
        let editDeleteImageIds = [];

        function setEditDeleteImageIds(ids) {
            editDeleteImageIds = Array.isArray(ids) ? ids : [];
            const el = document.getElementById('edit_delete_image_ids');
            if (el) el.value = JSON.stringify(editDeleteImageIds);
        }

        function localTodayStr() {
            const d = new Date();
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        }

        function showBookedDatesError(message) {
            if (window.$ && $.alert) {
                $.alert({ title: 'Invalid Date', content: message, type: 'red' });
                return;
            }
            alert(message);
        }

        function setBookedDates(prefix, data) {
            bookedDatesState[prefix] = Array.isArray(data) ? data : [];
            renderBookedDates(prefix);
        }

        function renderBookedDates(prefix) {
            const listEl = document.getElementById(`${prefix}_booked_dates_list`);
            const hiddenEl = document.getElementById(`${prefix}_booked_dates`);
            if (!listEl || !hiddenEl) return;

            hiddenEl.value = JSON.stringify(bookedDatesState[prefix] || []);
            listEl.innerHTML = '';

            (bookedDatesState[prefix] || []).forEach((item, idx) => {
                const pill = document.createElement('div');
                pill.style.display = 'inline-flex';
                pill.style.alignItems = 'center';
                pill.style.gap = '8px';
                pill.style.padding = '8px 10px';
                pill.style.border = '1px solid #e2e8f0';
                pill.style.borderRadius = '999px';
                pill.style.background = '#f8fafc';
                pill.style.fontWeight = '800';
                pill.style.color = '#0f172a';

                const label = document.createElement('span');
                if (typeof item === 'string') {
                    label.textContent = item;
                } else if (item && typeof item === 'object' && item.start && item.end) {
                    label.textContent = `${item.start} → ${item.end}`;
                } else {
                    label.textContent = 'Invalid';
                }

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.style.border = 'none';
                removeBtn.style.background = '#fee2e2';
                removeBtn.style.color = '#991b1b';
                removeBtn.style.width = '26px';
                removeBtn.style.height = '26px';
                removeBtn.style.borderRadius = '999px';
                removeBtn.style.cursor = 'pointer';
                removeBtn.style.fontWeight = '900';
                removeBtn.textContent = '×';
                removeBtn.onclick = () => {
                    bookedDatesState[prefix].splice(idx, 1);
                    renderBookedDates(prefix);
                };

                pill.appendChild(label);
                pill.appendChild(removeBtn);
                listEl.appendChild(pill);
            });
        }

        function addSingleDate(prefix, dateStr) {
            if (!dateStr) return;
            const today = localTodayStr();
            if (dateStr < today) {
                showBookedDatesError('Dates cannot be in the past.');
                return;
            }
            if ((bookedDatesState[prefix] || []).some((x) => x === dateStr)) {
                return;
            }
            bookedDatesState[prefix].push(dateStr);
            renderBookedDates(prefix);
        }

        function addDateRange(prefix, startStr, endStr) {
            if (!startStr || !endStr) return;
            const today = localTodayStr();
            if (startStr < today || endStr < today) {
                showBookedDatesError('Dates cannot be in the past.');
                return;
            }
            if (endStr < startStr) {
                showBookedDatesError('End date must be after or equal to start date.');
                return;
            }
            bookedDatesState[prefix].push({ start: startStr, end: endStr });
            renderBookedDates(prefix);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const addTerms = document.getElementById('add_agree_vehicle_terms');
            const addSubmit = document.getElementById('addVehicleSubmitBtn');
            if (addTerms && addSubmit) {
                const sync = () => {
                    addSubmit.disabled = !addTerms.checked;
                    addSubmit.style.opacity = addTerms.checked ? '1' : '0.55';
                    addSubmit.style.cursor = addTerms.checked ? 'pointer' : 'not-allowed';
                };
                addTerms.addEventListener('change', sync);
                sync();
            }

        });

        function openMyCarsTermsModal() {
            const m = document.getElementById('myCarsTermsModal');
            if (!m) return;
            m.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        function closeMyCarsTermsModal() {
            const m = document.getElementById('myCarsTermsModal');
            if (!m) return;
            m.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function editVehicle(id, name, license_plate, brand_id, type_id, price, status_id, trans_id, fuel_id, seats, color, year_model, images) {
            document.getElementById('editForm').action = '/my-cars/' + id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_license_plate').value = license_plate || '';
            document.getElementById('edit_color').value = color || '';
            document.getElementById('edit_year').value = year_model || '';
            setEditDeleteImageIds([]);
            
            if(brand_id) document.getElementById('edit_brand').value = brand_id;
            if(type_id) document.getElementById('edit_type').value = type_id;
            
            if(status_id) {
                const radios = document.querySelectorAll('#editForm input[name="lib_availability_status_id"]');
                radios.forEach(r => {
                    if(r.value == status_id) {
                        r.checked = true;
                        r.nextElementSibling.style.background = 'var(--accent)';
                        r.nextElementSibling.style.color = 'white';
                        r.nextElementSibling.style.borderColor = 'var(--accent)';
                    } else {
                        r.checked = false;
                        r.nextElementSibling.style.background = 'transparent';
                        r.nextElementSibling.style.color = '#64748b';
                        r.nextElementSibling.style.borderColor = '#cbd5e1';
                    }
                });
            }
            
            if(trans_id) document.getElementById('edit_transmission').value = trans_id;
            if(fuel_id) document.getElementById('edit_fuel').value = fuel_id;
            
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_seating').value = seats;
            
            const grid = document.getElementById('editPrimaryImageGrid');
            grid.innerHTML = '';
            const container = document.getElementById('editPrimaryImageContainer');
            if(images && images.length > 0) {
                container.style.display = 'block';
                images.forEach(img => {
                    const div = document.createElement('div');
                    div.style.minWidth = '100px';
                    div.style.textAlign = 'center';
                    div.style.position = 'relative';
                    
                    const elImg = document.createElement('img');
                    elImg.src = img.url;
                    elImg.style.width = '100px';
                    elImg.style.height = '70px';
                    elImg.style.objectFit = 'cover';
                    elImg.style.borderRadius = '6px';
                    elImg.style.marginBottom = '5px';

                    const deleteBtn = document.createElement('button');
                    deleteBtn.type = 'button';
                    deleteBtn.style.position = 'absolute';
                    deleteBtn.style.top = '-8px';
                    deleteBtn.style.right = '-8px';
                    deleteBtn.style.width = '28px';
                    deleteBtn.style.height = '28px';
                    deleteBtn.style.borderRadius = '999px';
                    deleteBtn.style.border = '1px solid #fecaca';
                    deleteBtn.style.background = '#fee2e2';
                    deleteBtn.style.color = '#991b1b';
                    deleteBtn.style.display = 'inline-flex';
                    deleteBtn.style.alignItems = 'center';
                    deleteBtn.style.justifyContent = 'center';
                    deleteBtn.style.cursor = 'pointer';
                    deleteBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#991b1b" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>';
                    
                    const radio = document.createElement('input');
                    radio.type = 'radio';
                    radio.name = 'primary_image_id';
                    radio.value = img.id;
                    if(img.is_primary) radio.checked = true;

                    deleteBtn.onclick = () => {
                        const doDelete = () => {
                            if (!editDeleteImageIds.includes(img.id)) {
                                setEditDeleteImageIds([...editDeleteImageIds, img.id]);
                            }
                            div.remove();
                            if (grid.children.length === 0) {
                                container.style.display = 'none';
                            }
                        };

                        if (window.$ && $.confirm) {
                            $.confirm({
                                title: 'Delete Photo',
                                content: 'Are you sure you want to delete this photo?',
                                type: 'red',
                                buttons: {
                                    Delete: { btnClass: 'btn-red', action: doDelete },
                                    Close: function () {},
                                }
                            });
                            return;
                        }

                        if (confirm('Are you sure you want to delete this photo?')) {
                            doDelete();
                        }
                    };
                    
                    div.appendChild(deleteBtn);
                    div.appendChild(elImg);
                    div.appendChild(document.createElement('br'));
                    div.appendChild(radio);
                    grid.appendChild(div);
                });
            } else {
                container.style.display = 'none';
            }
            
            document.getElementById('editVehicleModal').style.display = 'flex';
        }
        
        // CSS for cards hover
        document.querySelectorAll('.vehicle-card').forEach(card => {
            card.addEventListener('mouseenter', () => { card.style.transform = 'translateY(-5px)'; card.style.boxShadow = 'var(--shadow-md)'; });
            card.addEventListener('mouseleave', () => { card.style.transform = 'translateY(0)'; card.style.boxShadow = 'var(--shadow-sm)'; });
        });
    </script>
</x-member-layout>
