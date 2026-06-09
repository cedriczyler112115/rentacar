<x-member-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2>{{ __('Add Location Price') }}</h2>
                <p style="color: #64748b; margin-top: 5px;">Region and province must match existing entries.</p>
            </div>
            <a href="{{ route('municipalities.index') }}" class="btn btn-outline" style="display:inline-flex; align-items:center; gap:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                Back
            </a>
        </div>
    </x-slot>

    <div class="container" style="padding: 0px 20px 40px 20px; max-width: 900px;">
        <div style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: var(--shadow-sm); overflow: hidden;">
            <form method="POST" action="{{ route('municipalities.store') }}" style="padding: 24px;">
                @csrf

                @if ($errors->any())
                    <div style="background: #fee2e2; border-left: 4px solid #ef4444; color: #b91c1c; padding: 15px 20px; border-radius: 0 8px 8px 0; margin-bottom: 20px;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display:block; font-weight: 800; color:#0f172a; margin-bottom: 8px;">Region</label>
                        <select id="region" name="region" required style="width:100%; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 10px;">
                            <option value="">Select region</option>
                            @foreach($regions as $r)
                                <option value="{{ $r }}" {{ old('region') === $r ? 'selected' : '' }}>{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block; font-weight: 800; color:#0f172a; margin-bottom: 8px;">Province</label>
                        <select id="province" name="province" required style="width:100%; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 10px;">
                            <option value="">Select province</option>
                        </select>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr; gap: 16px; margin-top: 16px;">
                    <div>
                        <label style="display:block; font-weight: 800; color:#0f172a; margin-bottom: 8px;">Municipality</label>
                        <input type="text" name="municipality" required value="{{ old('municipality') }}" placeholder="Enter municipality" style="width:100%; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 10px;">
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap: 10px; margin-top: 20px;">
                    <a href="{{ route('municipalities.index') }}" class="btn btn-outline" style="display:inline-flex; align-items:center; gap:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8"/><path d="M7 3v5h8"/></svg>
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const regionProvinces = @json($regionProvinces);
            const regionEl = document.getElementById('region');
            const provinceEl = document.getElementById('province');

            const setProvinces = (region) => {
                provinceEl.innerHTML = '<option value="">Select province</option>';
                if (!region || !regionProvinces[region]) return;
                regionProvinces[region].forEach((p) => {
                    const opt = document.createElement('option');
                    opt.value = p;
                    opt.textContent = p;
                    provinceEl.appendChild(opt);
                });
            };

            regionEl.addEventListener('change', () => setProvinces(regionEl.value));

            const oldRegion = @json(old('region'));
            const oldProvince = @json(old('province'));
            if (oldRegion) {
                setProvinces(oldRegion);
                if (oldProvince) provinceEl.value = oldProvince;
            }
        });
    </script>
</x-member-layout>
