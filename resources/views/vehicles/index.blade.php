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
                <h2>Browse Vehicles</h2>
                <p style="color: #64748b; margin-top: 5px;">Explore available vehicles, compare options, and find the right ride for your trip.</p>
            </div>
        </div>
    </x-slot>

    <div class="container" style="padding: 0px 20px;">
        <div class="vehicle-layout">
            
            <!-- Sidebar for Filtering -->
            <aside class="filter-sidebar">
                <h3 style="margin-bottom: 20px; font-size: 1.3rem; color: var(--primary);">Filters</h3>
                
                <form action="{{ route('vehicles.index') }}" method="GET">
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px;">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or Brand..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px;">Brand</label>
                        <select name="lib_brand_id" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            <option value="">All Brands</option>
                            @foreach($brands as $b)
                                <option value="{{ $b->id }}" {{ request('lib_brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px;">Price Range (₱)</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px;">Type</label>
                        <select name="type" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            <option value="">All Types</option>
                            @foreach($types as $t)
                                <option value="{{ $t->id }}" {{ request('type') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px;">Transmission</label>
                        <select name="transmission" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            <option value="">Any</option>
                            @foreach($transmissions as $tr)
                                <option value="{{ $tr->id }}" {{ request('transmission') == $tr->id ? 'selected' : '' }}>{{ $tr->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px;">Fuel Type</label>
                        <select name="fuel_type" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            <option value="">Any</option>
                            @foreach($fuels as $f)
                                <option value="{{ $f->id }}" {{ request('fuel_type') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px;">Availability Status</label>
                        <select name="availability_status" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            <option value="">Any</option>
                            @foreach($statuses as $s)
                                <option value="{{ $s->id }}" {{ (string)request('availability_status') === (string)$s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px;">Minimum Seats</label>
                        <input type="number" name="seating_capacity" value="{{ request('seating_capacity') }}" placeholder="e.g. 4" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px;">Sort By</label>
                        <select name="sort" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="price_low_high" {{ request('sort') == 'price_low_high' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high_low" {{ request('sort') == 'price_high_low' ? 'selected' : '' }}>Price: High to Low</option>
                        </select>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary" style="flex: 1; padding: 10px;">Apply Filter</button>
                        <a href="{{ route('vehicles.index') }}" class="btn btn-outline" style="padding: 10px; text-align: center;">Clear</a>
                    </div>
                </form>
            </aside>

            <!-- Main Listing Area -->
            <div class="vehicle-main">
                
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
                                    <span style="font-size: .75rem; font-weight: 400; color: var(--accent);">Starts at</span> <span style="font-size: .8rem; font-weight: 800; color: var(--accent);"> ₱{{ number_format($vehicle->price_per_day, 2) }}</span>
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
                                    @php
                                        $ownerId = (int)($vehicle->user_id ?? 0);
                                        $or = $ownerId > 0 ? (($ownerRatings[$ownerId] ?? null)) : null;
                                        $avg = $or ? (float)($or['avg'] ?? 0) : 0;
                                        $cnt = $or ? (int)($or['count'] ?? 0) : 0;
                                    @endphp
                                    <div style="grid-column: 1 / -1; display:flex; align-items:center; justify-content:space-between; gap: 10px; padding-top: 10px; border-top: 1px dashed #e2e8f0;">
                                        <div style="display:flex; align-items:center; gap: 8px; min-width: 0;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><path d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8"/></svg>
                                            <span style="font-weight: 700; color:#64748b;">Owner:</span>
                                            <a href="#" class="owner-profile-link owner-profile-link--muted" data-owner-id="{{ (int)($vehicle->user_id ?? 0) }}" data-vehicle-id="{{ (int)$vehicle->id }}" data-vehicle-name="{{ $vehicle->name }}">
                                                {{ $vehicle->user->name ?? 'N/A' }}
                                            </a>
                                        </div>
                                        <div style="display:inline-flex; align-items:center; gap:6px; flex: 0 0 auto;">
                                            <a href="#" class="owner-profile-link owner-profile-link--muted" data-owner-id="{{ (int)($vehicle->user_id ?? 0) }}" data-vehicle-id="{{ (int)$vehicle->id }}" data-vehicle-name="{{ $vehicle->name }}" style="display:inline-flex; align-items:center; gap:6px;">
                                                <span style="font-weight: 900;">★ {{ $cnt > 0 ? number_format($avg, 1) : '—' }}</span>
                                                <span style="font-weight: 800; font-size: 0.85rem;">({{ $cnt }})</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                    @php $statusName = strtolower($vehicle->libAvailabilityStatus->name ?? ''); @endphp
                                    @if(in_array($statusName, ['available', 'pending'], true))
                                        <a href="{{ route('book.now', $vehicle) }}" class="btn btn-primary" style="flex: 1; min-width: 160px; padding: 10px; text-align: center; text-decoration: none; font-weight: 700; font-size: 1.05rem;">Book Now!</a>
                                    @else
                                        <button type="button" class="btn btn-outline" style="flex: 1; min-width: 160px; padding: 10px; text-align: center; font-weight: 800; font-size: 1.05rem; cursor: not-allowed; opacity: 0.7;" disabled>
                                            Unavailable
                                        </button>
                                    @endif
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

    <div id="ownerProfileModal" style="display:none; position: fixed; inset: 0; background: rgba(2,6,23,0.85); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
        <div id="ownerProfileBackdrop" style="position:absolute; inset:0;"></div>
        <div style="position: relative; z-index: 1; width: 100%; max-width: 1080px; background: white; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.35); max-height: 85vh; display:flex; flex-direction:column;">
            <div style="padding: 14px 16px; background: #0f172a; color: white; display:flex; justify-content:space-between; gap: 10px; align-items:center;">
                <div style="font-weight: 900; letter-spacing: 0.2px;">Owner Profile</div>
                <button type="button" id="ownerProfileClose" style="background:none; border:none; color:white; font-size: 2rem; line-height: 1; cursor:pointer; opacity:0.85;">&times;</button>
            </div>
            <div style="padding: 16px; background: #f8fafc; overflow:auto;">
                <div id="ownerProfileGrid" style="display:grid; grid-template-columns: 1fr; gap: 14px;">
                    <div id="ownerLeftCard" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px;"></div>
                    <div id="ownerRightCard" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px;"></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (min-width: 980px) {
            #ownerProfileGrid { grid-template-columns: 1fr 1fr !important; }
        }
        .owner-profile-link--muted { color: #64748b; text-decoration: none; cursor: pointer; }
        .owner-profile-link--muted:hover { color: var(--accent); }
    </style>

    <!-- Scripts -->
    <script>
        let modalImageSet = [];
        let modalImageIndex = 0;
        let modalCurrentVehicleId = null;

        <!-- scripts intact -->

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

        <!-- no primary set -->

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

        function editVehicle(id, name, brand_id, type_id, price, status_id, trans_id, fuel_id, seats, color, displacement, year_model, images) {
            document.getElementById('editForm').action = '/vehicles/' + id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_color').value = color || '';
            document.getElementById('edit_displacement').value = displacement || '';
            document.getElementById('edit_year').value = year_model || '';
            
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
                    
                    const elImg = document.createElement('img');
                    elImg.src = img.url;
                    elImg.style.width = '100px';
                    elImg.style.height = '70px';
                    elImg.style.objectFit = 'cover';
                    elImg.style.borderRadius = '6px';
                    elImg.style.marginBottom = '5px';
                    
                    const radio = document.createElement('input');
                    radio.type = 'radio';
                    radio.name = 'primary_image_id';
                    radio.value = img.id;
                    if(img.is_primary) radio.checked = true;
                    
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

    <script>
        (function () {
            function esc(s) {
                return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            }
            function stars(r) {
                const v = Number(r || 0);
                let out = '';
                for (let i = 1; i <= 5; i++) {
                    out += '<span style="font-weight:900; color:' + (i <= v ? 'var(--accent)' : '#cbd5e1') + ';">★</span>';
                }
                return out;
            }

            const modal = document.getElementById('ownerProfileModal');
            const backdrop = document.getElementById('ownerProfileBackdrop');
            const closeBtn = document.getElementById('ownerProfileClose');
            const left = document.getElementById('ownerLeftCard');
            const right = document.getElementById('ownerRightCard');
            let loading = false;

            function openModal() {
                if (!modal) return;
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                if (!modal) return;
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }

            backdrop?.addEventListener('click', closeModal);
            closeBtn?.addEventListener('click', closeModal);
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

            document.addEventListener('click', (e) => {
                const link = e.target.closest('.owner-profile-link');
                if (!link) return;
                e.preventDefault();
                const ownerId = link.getAttribute('data-owner-id');
                const vehicleId = link.getAttribute('data-vehicle-id');
                if (!ownerId || loading) return;
                loading = true;
                openModal();
                if (left) left.innerHTML = '<div style="color:#64748b; font-weight: 900;">Loading profile…</div>';
                if (right) right.innerHTML = '<div style="color:#64748b; font-weight: 900;">Loading reviews…</div>';

                let perPage = 10;
                let showSelectedOnly = false;
                let nextUrl = null;
                let prevUrl = null;

                function renderRightControls(p) {
                    const total = Number(p?.total || 0);
                    const cur = Number(p?.current_page || 1);
                    const last = Number(p?.last_page || 1);
                    const pp = p?.per_page === 'all' ? 'all' : Number(p?.per_page || perPage);
                    const pageText = pp === 'all' ? (total + ' total') : ('Page ' + cur + ' of ' + last + ' • ' + total + ' total');
                    const opt = (v, label) => '<option value="' + v + '"' + (String(pp) === String(v) ? ' selected' : '') + '>' + label + '</option>';
                    const prevDisabled = p?.prev_page_url ? '' : 'disabled';
                    const nextDisabled = p?.next_page_url ? '' : 'disabled';
                    const hideBtns = pp === 'all' ? 'display:none;' : '';
                    return (
                        '<div style="margin-top: 12px; display:flex; justify-content:space-between; gap: 10px; flex-wrap: wrap; align-items:center;">' +
                            '<div style="color:#64748b; font-weight: 900; font-size: 0.9rem;">' + esc(pageText) + '</div>' +
                            '<div style="display:flex; gap: 10px; align-items:center; flex-wrap: wrap;">' +
                                '<span style="color:#64748b; font-weight: 900; font-size: 0.85rem;">Page size</span>' +
                                '<select id="ownerModalPageSize" style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px; font-weight: 800;">' +
                                    opt(10, '10') + opt(20, '20') + opt(50, '50') + opt('all', 'ALL') +
                                '</select>' +
                                '<button type="button" id="ownerModalPrev" class="btn btn-outline" style="padding: 10px 14px; font-size: 0.95rem; ' + hideBtns + '" ' + prevDisabled + '>Prev</button>' +
                                '<button type="button" id="ownerModalNext" class="btn btn-outline" style="padding: 10px 14px; font-size: 0.95rem; ' + hideBtns + '" ' + nextDisabled + '>Next</button>' +
                            '</div>' +
                        '</div>'
                    );
                }

                function renderOwnerReviewsRight(reviews, pagination, selectedVehicle) {
                    const items = Array.isArray(reviews) ? reviews : [];
                    const title = showSelectedOnly ? 'Reviews of this vehicle' : 'All Reviews';
                    const toggleLabel = showSelectedOnly ? 'Show all reviews' : 'Show Reviews of this vehicle';
                    const toggleDisabled = vehicleId ? '' : 'disabled';
                    const toggleStyle = vehicleId
                        ? (!showSelectedOnly ? 'background: rgba(16, 185, 129, 0.14); border: 1px solid rgba(16, 185, 129, 0.28); color: #10b981;' : 'background: rgba(245, 158, 11, 0.14); border: 1px solid rgba(245, 158, 11, 0.28); color: var(--accent);')
                        : 'background: rgba(15, 23, 42, 0.04); border: 1px solid rgba(15, 23, 42, 0.08); color:#94a3b8; cursor: not-allowed; opacity: 0.8;';
                    const showVehicleMeta = !showSelectedOnly && selectedVehicle && vehicleId;
                    const metaColor = selectedVehicle?.color ? (' • ' + selectedVehicle.color) : '';
                    const metaYear = selectedVehicle?.year_model ? (' • ' + selectedVehicle.year_model) : '';
                    let html = '<div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap: wrap;">' +
                        '<div style="font-weight: 900; color: var(--primary); font-size: 1.2rem;">' + esc(title) + '</div>' +
                        '<button type="button" id="ownerModalFilterToggle" ' + toggleDisabled + ' aria-pressed="' + (showSelectedOnly ? 'true' : 'false') + '" style="display:inline-flex; align-items:center; gap:8px; padding: 8px 12px; border-radius: 12px; font-weight: 900; cursor: pointer; ' + toggleStyle + '">' +
                            '<span style="display:flex; flex-direction:column; align-items:flex-start; gap: 2px;">' +
                                '<span>' + esc(toggleLabel) + '</span>' +
                                (showVehicleMeta ? ('<span style="font-weight: 800; font-size: 0.85rem; color: #64748b;">' + esc((selectedVehicle?.name || 'Vehicle') + metaColor + metaYear) + '</span>') : '') +
                            '</span>' +
                        '</button>' +
                    '</div>';
                    if (items.length === 0) {
                        html += '<div style="margin-top: 10px; color:#94a3b8; font-weight: 800;">No reviews yet.</div>';
                        html += renderRightControls(pagination);
                        return html;
                    }
                    const grouped = {};
                    items.forEach(rv => {
                        const vid = rv.vehicle?.id || '0';
                        if (!grouped[vid]) grouped[vid] = { vehicle: rv.vehicle, items: [] };
                        grouped[vid].items.push(rv);
                    });
                    Object.keys(grouped).forEach(k => {
                        const g = grouped[k];
                        const vname = g.vehicle?.name || 'Vehicle';
                        const color = g.vehicle?.color ? (' • ' + g.vehicle.color) : '';
                        const year = g.vehicle?.year_model ? (' • ' + g.vehicle.year_model) : '';
                        html += '<div style="margin-top: 12px; padding-top: 12px; border-top: 1px dashed #e2e8f0;">' +
                            '<div style="font-weight: 900; color:#0f172a;">' + esc(vname + color + year) + '</div>' +
                            '<div style="margin-top: 10px; display:flex; flex-direction:column; gap: 10px;">';
                        g.items.forEach(rv => {
                            const who = rv.reviewer?.name || 'User';
                            const when = rv.created_at ? new Date(rv.created_at).toLocaleDateString() : '';
                            html += '<div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; background: #f8fafc;">' +
                                '<div style="display:flex; justify-content:space-between; gap:10px; flex-wrap: wrap;">' +
                                    '<div style="font-weight: 400; color:#0f172a;"><b>' + esc(who) + '</b> • '+stars(rv.rating)+ '</div>' +
                                    '<div style="font-weight: 800; color:#94a3b8; font-size: 0.85rem;">' + esc(when) + '</div>' +
                                '</div>' +
                                '<div style="margin-top: 10px; color:#0f172a; font-weight: 400; font-size: 0.9rem; white-space: pre-wrap;">' + esc(rv.comment) + '</div>' +
                            '</div>';
                        });
                        html += '</div></div>';
                    });
                    html += renderRightControls(pagination);
                    return html;
                }

                function buildBaseUrl() {
                    const base = '{{ route('owners.profile', ['user' => '__ID__']) }}'.replace('__ID__', ownerId);
                    const u = new URL(base, window.location.origin);
                    if (vehicleId) u.searchParams.set('vehicle_id', String(vehicleId));
                    u.searchParams.set('per_page', String(perPage));
                    if (showSelectedOnly && vehicleId) u.searchParams.set('only_vehicle', '1');
                    return u.toString();
                }

                function bindControls() {
                    const prev = document.getElementById('ownerModalPrev');
                    const next = document.getElementById('ownerModalNext');
                    const sel = document.getElementById('ownerModalPageSize');
                    prev?.addEventListener('click', () => { if (prevUrl) fetchOwner(prevUrl); });
                    next?.addEventListener('click', () => { if (nextUrl) fetchOwner(nextUrl); });
                    sel?.addEventListener('change', () => {
                        const v = String(sel.value || '10');
                        perPage = v === 'all' ? 'all' : parseInt(v, 10);
                        nextUrl = null;
                        prevUrl = null;
                        fetchOwner(null);
                    });

                    const cb = document.getElementById('ownerModalFilterSelected');
                    const toggle = document.getElementById('ownerModalFilterToggle');
                    toggle?.addEventListener('click', () => {
                        if (!vehicleId) return;
                        showSelectedOnly = !showSelectedOnly;
                        nextUrl = null;
                        prevUrl = null;
                        fetchOwner(null);
                    });
                }

                function fetchOwner(url) {
                    const finalUrl = url || buildBaseUrl();
                    fetch(finalUrl, { headers: { 'Accept': 'application/json' } })
                        .then(r => r.ok ? r.json() : Promise.reject(new Error('Unable to load owner profile.')))
                        .then(data => {
                            const o = data.owner || {};
                            const photo = o.profile_photo_url ? ('<img src="' + esc(o.profile_photo_url) + '" alt="" style="width:72px; height:72px; border-radius: 16px; object-fit: cover; border: 1px solid #e2e8f0;">') : ('<div style="width:72px; height:72px; border-radius: 16px; background:#f1f5f9; border: 1px solid #e2e8f0; display:flex; align-items:center; justify-content:center; font-weight:900; color:#94a3b8;">N/A</div>');
                            const avg = Number(o.avg_rating || 0).toFixed(1);
                            const cnt = Number(o.total_reviews || 0);
                            if (left) {
                                const proofs = Array.isArray(data.legitimacy_proofs) ? data.legitimacy_proofs : [];
                                left.innerHTML =
                                    '<div style="display:flex; gap: 12px; align-items:center;">' +
                                        photo +
                                        '<div style="min-width:0;">' +
                                            '<div style="font-weight: 900; color:#0f172a; font-size: 1.1rem;">' + esc(o.name) + '</div>' +
                                            '<div style="margin-top: 4px; color:#64748b; font-weight: 700;">' + esc(o.email) + '</div>' +
                                        '</div>' +
                                    '</div>' +
                                    '<div style="margin-top: 12px; display:flex; flex-wrap:wrap; gap: 10px;">' +
                                        '<span style="display:inline-flex; align-items:center; gap:8px; padding: 8px 12px; border-radius: 12px; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.25); color: #b45309; font-weight: 900;">Owner Average Rating ★' + avg + ' (' + cnt + ')</span>' +
                                        '<span style="display:inline-flex; align-items:center; gap:8px; padding: 8px 12px; border-radius: 12px; background: rgba(15, 23, 42, 0.06); border: 1px solid rgba(15, 23, 42, 0.12); color: #0f172a; font-weight: 900;">Vehicles: ' + (o.vehicles_count || 0) + '</span>' +
                                    '</div>' +
                                    '<div style="margin-top: 12px; border-top: 1px solid #e2e8f0; padding-top: 12px;">' +
                                        '<div style="color:#64748b; font-weight: 900; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.45px;">Owner Address</div>' +
                                        '<div style="margin-top: 6px; font-weight: 400; color:#0f172a; white-space: pre-wrap;">' + esc(o.address || '—') + '</div>' +
                                    '</div>' +
                                    '<div style="margin-top: 12px; border-top: 1px solid #e2e8f0; padding-top: 12px;">' +
                                        '<div style="color:#64748b; font-weight: 900; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.45px;">Proof of Legitimacy</div>' +
                                        (proofs.length > 0
                                            ? ('<div style="margin-top: 8px; display:grid; grid-template-columns: repeat(3, 1fr); gap: 8px;">' +
                                                proofs.map((p, idx) => '<a href=\"' + esc(p.url) + '\" class=\"owner-proof-photo\" data-src=\"' + esc(p.url) + '\" data-idx=\"' + idx + '\" style=\"display:block; border-radius:10px; overflow:hidden; border:1px solid #e2e8f0; background:white;\"><img src=\"' + esc(p.url) + '\" alt=\"Proof\" style=\"width:100%; height:90px; object-fit:cover;\"></a>').join('') +
                                              '</div>')
                                            : '<div style="margin-top: 8px; color:#94a3b8; font-weight:800;">No proof images.</div>') +
                                    '</div>' +
                                    '<div style="margin-top: 12px; border-top: 1px solid #e2e8f0; padding-top: 12px;">' +
                                        '<div style="color:#64748b; font-weight: 900; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.45px;">About the owner</div>' +
                                        '<div style="margin-top: 8px; font-weight: 300; color:#0f172a;">' + String(o.about_owner || '').replace(/<script[\s\S]*?>[\s\S]*?<\/script>/gi, '') + '</div>' +
                                    '</div>';
                            }

                            const pag = data.pagination || {};
                            nextUrl = pag.next_page_url || null;
                            prevUrl = pag.prev_page_url || null;
                            if (right) {
                                right.innerHTML = renderOwnerReviewsRight(data.reviews, pag, data.selected_vehicle);
                            }
                            bindControls();
                        })
                        .catch(err => {
                            if (left) left.innerHTML = '<div style="color:#b91c1c; font-weight: 900;">' + esc(err?.message || 'Unable to load.') + '</div>';
                            if (right) right.innerHTML = '';
                        })
                        .finally(() => { loading = false; });
                }

                fetchOwner(null);
            });

            const proofModal = document.createElement('div');
            proofModal.id = 'ownerProofCarouselModal';
            proofModal.style.cssText = 'display:none; position:fixed; inset:0; background:rgba(2,6,23,0.88); z-index:100200; align-items:center; justify-content:center; padding:20px;';
            proofModal.innerHTML = `
                <div id="ownerProofBackdrop" style="position:absolute; inset:0;"></div>
                <div style="position:relative; z-index:1; width:100%; max-width:980px; background:#111827; border-radius: 12px; overflow:hidden; border:1px solid #374151;">
                    <div style="padding:10px 12px; background:#0f172a; color:white; display:flex; justify-content:space-between; align-items:center;">
                        <div style="font-weight:900;">Proof Photos</div>
                        <div style="display:flex; gap:8px;">
                            <button id="ownerProofPrev" type="button" style="background:#1f2937; color:#e5e7eb; border:1px solid #374151; padding:8px 10px; border-radius:8px;">Prev</button>
                            <button id="ownerProofNext" type="button" style="background:#1f2937; color:#e5e7eb; border:1px solid #374151; padding:8px 10px; border-radius:8px;">Next</button>
                            <button id="ownerProofClose" type="button" style="background:#ef4444; color:white; border:1px solid #b91c1c; padding:8px 10px; border-radius:8px;">Close</button>
                        </div>
                    </div>
                    <div style="background:#111827; display:flex; align-items:center; justify-content:center; min-height:420px;">
                        <img id="ownerProofImg" src="" alt="" style="max-width:100%; max-height:420px; object-fit:contain;"/>
                    </div>
                </div>
            `;
            document.body.appendChild(proofModal);

            let ownerProofImages = [];
            let ownerProofCur = 0;
            function ownerProofOpen(startIndex) {
                const anchors = Array.from(document.querySelectorAll('.owner-proof-photo'));
                ownerProofImages = anchors.map(a => a.getAttribute('data-src')).filter(Boolean);
                ownerProofCur = Math.max(0, Math.min(startIndex || 0, ownerProofImages.length - 1));
                const img = document.getElementById('ownerProofImg');
                if (img && ownerProofImages.length > 0) img.src = ownerProofImages[ownerProofCur];
                proofModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
            function ownerProofClose() {
                proofModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
            function ownerProofNav(offset) {
                if (ownerProofImages.length === 0) return;
                ownerProofCur = (ownerProofCur + offset + ownerProofImages.length) % ownerProofImages.length;
                const img = document.getElementById('ownerProofImg');
                if (img) img.src = ownerProofImages[ownerProofCur];
            }

            document.addEventListener('click', (e) => {
                const a = e.target.closest('.owner-proof-photo');
                if (!a) return;
                e.preventDefault();
                const idx = parseInt(a.getAttribute('data-idx') || '0', 10);
                ownerProofOpen(isNaN(idx) ? 0 : idx);
            });
            document.getElementById('ownerProofBackdrop')?.addEventListener('click', ownerProofClose);
            document.getElementById('ownerProofClose')?.addEventListener('click', ownerProofClose);
            document.getElementById('ownerProofPrev')?.addEventListener('click', () => ownerProofNav(-1));
            document.getElementById('ownerProofNext')?.addEventListener('click', () => ownerProofNav(1));
            document.addEventListener('keydown', (e) => {
                if (proofModal.style.display === 'flex') {
                    if (e.key === 'Escape') ownerProofClose();
                    if (e.key === 'ArrowLeft') ownerProofNav(-1);
                    if (e.key === 'ArrowRight') ownerProofNav(1);
                }
            });
        })();
    </script>
</x-member-layout>
