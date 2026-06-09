<x-member-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2>{{ __('Price per Location') }}</h2>
                <p style="color: #64748b; margin-top: 5px;">Manage destination pricing by region, province, and municipality.</p>
            </div>
            <a href="{{ route('municipalities.create') }}" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Add Location Price
            </a>
        </div>
    </x-slot>

    <div class="container" style="padding: 0px 20px 40px 20px;">
        <div style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: var(--shadow-sm); overflow: hidden;">
            <div style="padding: 16px 18px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
                <form method="GET" action="{{ route('municipalities.index') }}" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search municipality / province / region" style="flex: 1; min-width: 240px; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
                    <select id="filter_region" name="region" style="min-width: 180px; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
                        <option value="">All Regions</option>
                        @foreach($regions as $r)
                            <option value="{{ $r }}" {{ request('region') === $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                    <select id="filter_province" name="province" style="min-width: 180px; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
                        <option value="">All Provinces</option>
                        @foreach($provinces as $p)
                            <option value="{{ $p }}" {{ request('province') === $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-outline" style="display:inline-flex; align-items:center; gap:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 3H2l8 9v7l4 2v-9z"/></svg>
                        Filter
                    </button>
                    <a href="{{ route('municipalities.index') }}" class="btn btn-outline" style="display:inline-flex; align-items:center; gap:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M6 6l1 16h10l1-16"/></svg>
                        Clear
                    </a>
                </form>
            </div>

            <div style="overflow-x:auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align:left; background: #0f172a; color: white;">
                            <th style="padding: 12px 14px; font-weight: 800;">Region</th>
                            <th style="padding: 12px 14px; font-weight: 800;">Province</th>
                            <th style="padding: 12px 14px; font-weight: 800;">Municipality</th>
                            <th style="padding: 12px 14px; font-weight: 800; text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($municipalities as $m)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 12px 14px; font-weight: 700; color: #0f172a;">{{ $m->region }}</td>
                                <td style="padding: 12px 14px; font-weight: 700; color: #0f172a;">{{ $m->province }}</td>
                                <td style="padding: 12px 14px; font-weight: 700; color: #0f172a;">{{ $m->municipality }}</td>
                                <td style="padding: 12px 14px; text-align:right;">
                                    <div style="display:inline-flex; gap:8px; align-items:center;">
                                        <a href="{{ route('municipalities.edit', $m) }}" class="btn btn-outline" style="display:inline-flex; align-items:center; gap:8px;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                                            Manage Prices
                                        </a>
                                        <form method="POST" action="{{ route('municipalities.destroy', $m) }}" class="confirm-delete" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline" style="border-color:#fecaca; color:#991b1b; display:inline-flex; align-items:center; gap:8px;">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#991b1b" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding: 40px 14px; text-align:center; color:#64748b; font-weight:700;">
                                    No locations found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top: 20px;">
            {{ $municipalities->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const regionProvinces = @json($regionProvinces);
            const regionEl = document.getElementById('filter_region');
            const provinceEl = document.getElementById('filter_province');
            const oldProvince = @json(request('province'));

            const setProvinces = (region, keepSelected) => {
                const current = keepSelected ? oldProvince : '';
                provinceEl.innerHTML = '<option value="">All Provinces</option>';
                if (!region || !regionProvinces[region]) return;
                regionProvinces[region].forEach((p) => {
                    const opt = document.createElement('option');
                    opt.value = p;
                    opt.textContent = p;
                    provinceEl.appendChild(opt);
                });
                if (current) provinceEl.value = current;
            };

            regionEl.addEventListener('change', () => {
                setProvinces(regionEl.value, false);
            });

            const currentRegion = @json(request('region'));
            if (currentRegion) setProvinces(currentRegion, true);
        });
    </script>
</x-member-layout>
