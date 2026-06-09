<x-member-layout>
    <x-slot name="header">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap: 12px; flex-wrap: wrap;">
            <div>
                <h2>{{ __('Owners Rating') }}</h2>
                <p style="color: #64748b; margin-top: 5px;">View owner average ratings and vehicle-specific reviews.</p>
            </div>
        </div>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('admin.partials.nav')
            </div>

            <div class="admin-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                <form id="ownerRatingsFilters" method="GET" action="{{ route('admin.owner-ratings.index') }}" style="display:flex; gap: 10px; flex-wrap: wrap; align-items: end; margin-bottom: 14px;">
                    <div style="flex: 1; min-width: 240px;">
                        <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Search Member</label>
                        <input type="text" id="ownerRatingsSearch" name="q" value="{{ $q ?? '' }}" placeholder="Name or email" style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                    </div>
                    <div style="min-width: 220px;">
                        <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Filter</label>
                        <select id="ownerRatingsFilter" name="filter" style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                            <option value="all" {{ ($filter ?? 'all') === 'all' ? 'selected' : '' }}>All</option>
                            <option value="has_reviews" {{ ($filter ?? '') === 'has_reviews' ? 'selected' : '' }}>Has Reviews</option>
                            <option value="no_reviews" {{ ($filter ?? '') === 'no_reviews' ? 'selected' : '' }}>No Reviews</option>
                            <option value="below3" {{ ($filter ?? '') === 'below3' ? 'selected' : '' }}>Owner Average Rating Below 3★</option>
                            <option value="above3" {{ ($filter ?? '') === 'above3' ? 'selected' : '' }}>Owner Average Rating 3★ and up</option>
                        </select>
                    </div>
                </form>

                <div style="display:flex; flex-direction:column; gap: 12px;">
                    @forelse($owners as $owner)
                        @php
                            $avg = $owner->reviews_received_avg_rating ? round((float)$owner->reviews_received_avg_rating, 1) : 0;
                            $cnt = (int)($owner->reviews_received_count ?? 0);
                            $vehCount = (int)($owner->vehicles_count ?? 0);
                            $ownerKey = 'owner-' . $owner->id;
                        @endphp
                        <div style="border: 1px solid #e2e8f0; border-radius: 14px; overflow:hidden; background: #ffffff;">
                            <button type="button" class="member-accordion-trigger" data-target="{{ $ownerKey }}" style="width: 100%; text-align:left; padding: 14px 16px; background: #0f172a; color: white; display:flex; justify-content:space-between; gap: 12px; flex-wrap: wrap; align-items:center; border: 0;">
                                <div>
                                    <div style="font-weight: 900; letter-spacing: 0.2px; font-size: 1rem;">{{ $owner->name }}</div>
                                    <div style="margin-top: 4px; color: rgba(226, 232, 240, 0.9); font-weight: 700; font-size: 0.9rem;">{{ $owner->email }}</div>
                                </div>
                                <div style="display:flex; gap: 10px; flex-wrap: wrap; align-items:center;">
                                    <span style="display:inline-flex; align-items:center; gap:8px; padding: 8px 12px; border-radius: 12px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.18); font-weight: 900;">
                                        Owner Average Rating ★{{ $cnt > 0 ? number_format($avg, 1) : '—' }} ({{ $cnt }})
                                    </span>
                                    <span style="display:inline-flex; align-items:center; gap:8px; padding: 8px 12px; border-radius: 12px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.18); font-weight: 900;">
                                        Vehicles: {{ $vehCount }}
                                    </span>
                                    <span class="member-accordion-icon" style="width: 38px; height: 38px; border-radius: 12px; display:inline-flex; align-items:center; justify-content:center; border: 1px solid rgba(255,255,255,0.18); background: rgba(255,255,255,0.06); color: rgba(226,232,240,0.9); font-weight: 900;">+</span>
                                </div>
                            </button>

                            <div id="{{ $ownerKey }}" class="member-accordion-panel" style="display:none; padding: 14px 16px; background: #f8fafc;">
                                <div style="display:flex; flex-direction:column; gap: 10px;">
                                    @forelse($owner->vehicles as $vehicle)
                                        @php
                                            $vAvg = $vehicle->reviews_avg_rating ? round((float)$vehicle->reviews_avg_rating, 1) : 0;
                                            $vCnt = (int)($vehicle->reviews_count ?? 0);
                                            $vehKey = 'veh-' . $owner->id . '-' . $vehicle->id;
                                        @endphp
                                        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; overflow:hidden;">
                                            <button type="button" class="vehicle-accordion-trigger" data-target="{{ $vehKey }}" style="width: 100%; text-align:left; padding: 12px 14px; background: white; display:flex; justify-content:space-between; gap: 10px; align-items:center;">
                                                <div>
                                                    <div style="font-weight: 900; color:#0f172a;">{{ $vehicle->name }}</div>
                                                    <div style="margin-top: 4px; color:#64748b; font-weight: 800; font-size: 0.85rem;">{{ $vehicle->license_plate ?? '—' }}</div>
                                                </div>
                                                <div style="display:flex; gap: 10px; align-items:center; flex-wrap: wrap;">
                                                    <span style="display:inline-flex; align-items:center; gap:8px; padding: 6px 10px; border-radius: 999px; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.25); color: #b45309; font-weight: 900; font-size: 0.85rem;">
                                                        ★{{ $vCnt > 0 ? number_format($vAvg, 1) : '—' }} ({{ $vCnt }})
                                                    </span>
                                                    <span class="vehicle-accordion-icon" style="width: 36px; height: 36px; border-radius: 10px; display:inline-flex; align-items:center; justify-content:center; border: 1px solid #e2e8f0; color:#64748b; font-weight: 900;">+</span>
                                                </div>
                                            </button>
                                            <div id="{{ $vehKey }}" class="vehicle-accordion-panel" style="display:none; padding: 12px 14px; border-top: 1px solid #e2e8f0; background: #ffffff;">
                                                @if($vCnt === 0)
                                                    <div style="color:#94a3b8; font-weight: 800;">No reviews for this vehicle.</div>
                                                @else
                                                    <div style="display:flex; flex-direction:column; gap: 10px;">
                                                        @foreach($vehicle->reviews as $review)
                                                            <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; background: #f8fafc;">
                                                                <div style="display:flex; justify-content:space-between; gap: 10px; flex-wrap: wrap;">
                                                                    <div style="font-weight: 900; color:#0f172a;">{{ $review->reviewer->name ?? 'User' }}</div>
                                                                    <div style="color:#94a3b8; font-weight: 800; font-size: 0.85rem;">{{ $review->created_at?->format('M d, Y') }}</div>
                                                                </div>
                                                                <div style="margin-top: 6px; display:flex; gap: 4px; align-items:center;">
                                                                    @for($i = 1; $i <= 5; $i++)
                                                                        <span style="font-weight: 900; color: {{ $i <= (int)$review->rating ? 'var(--accent)' : '#cbd5e1' }};">★</span>
                                                                    @endfor
                                                                </div>
                                                                <div style="margin-top: 10px; color:#0f172a; font-weight: 500; font-size: 0.9rem; white-space: pre-wrap;">{{ $review->comment }}</div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div style="color:#94a3b8; font-weight: 800;">No vehicles found for this owner.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="padding: 26px; text-align:center; background:#f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
                            <div style="font-weight: 900; color: var(--primary);">No owners found</div>
                            <div style="font-weight: 800; color: #64748b; margin-top: 6px;">Owners are users who have at least one vehicle.</div>
                        </div>
                    @endforelse
                </div>

                <div style="margin-top: 16px;">
                    {{ $owners->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            function bindAccordion(triggerSelector, panelPrefix, iconSelector) {
                document.querySelectorAll(triggerSelector).forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const id = btn.getAttribute('data-target');
                        const panel = document.getElementById(id);
                        if (!panel) return;
                        const icon = btn.querySelector(iconSelector);
                        const open = panel.style.display !== 'none';
                        panel.style.display = open ? 'none' : 'block';
                        if (icon) icon.textContent = open ? '+' : '–';
                    });
                });
            }

            bindAccordion('.member-accordion-trigger', 'owner-', '.member-accordion-icon');
            bindAccordion('.vehicle-accordion-trigger', 'veh-', '.vehicle-accordion-icon');

            if (!window.jQuery) return;
            const $form = $('#ownerRatingsFilters');
            const $q = $('#ownerRatingsSearch');
            const $filter = $('#ownerRatingsFilter');
            let t = null;
            function submitFilters() { $form.trigger('submit'); }
            $filter.on('change', submitFilters);
            $q.on('input', function () {
                clearTimeout(t);
                t = setTimeout(submitFilters, 400);
            });
        })();
    </script>
</x-member-layout>
