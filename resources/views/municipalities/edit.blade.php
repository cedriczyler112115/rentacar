<x-member-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2>{{ __('Edit Location Price') }}</h2>
                <p style="color: #64748b; margin-top: 5px;">Only the price can be updated.</p>
            </div>
            <a href="{{ route('municipalities.index') }}" class="btn btn-outline" style="display:inline-flex; align-items:center; gap:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                Back
            </a>
        </div>
    </x-slot>

    <div class="container" style="padding: 0px 20px 40px 20px; max-width: 900px;">
        <div style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: var(--shadow-sm); overflow: hidden;">
            <form method="POST" action="{{ route('municipalities.update', $municipality) }}" style="padding: 24px;">
                @csrf
                @method('PUT')

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
                        <input type="text" value="{{ $municipality->region }}" readonly style="width:100%; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 10px; background:#f8fafc;">
                    </div>
                    <div>
                        <label style="display:block; font-weight: 800; color:#0f172a; margin-bottom: 8px;">Province</label>
                        <input type="text" value="{{ $municipality->province }}" readonly style="width:100%; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 10px; background:#f8fafc;">
                    </div>
                </div>

                <div style="margin-top: 16px;">
                    <label style="display:block; font-weight: 800; color:#0f172a; margin-bottom: 8px;">Municipality</label>
                    <input type="text" value="{{ $municipality->municipality }}" readonly style="width:100%; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 10px; background:#f8fafc;">
                </div>

                <div style="margin-top: 18px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
                    <div style="font-weight: 900; color:#0f172a; margin-bottom: 6px;">Car Type Pricing</div>
                    <div style="color:#64748b; font-weight: 700; font-size: 0.9rem; margin-bottom: 12px;">
                        Set specific rates per car type for this municipality.
                    </div>

                    <div style="overflow-x:auto; border: 1px solid #e2e8f0; border-radius: 12px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="text-align:left; background: #0f172a; color: white;">
                                    <th style="padding: 12px 14px; font-weight: 900;">Car Type</th>
                                    <th style="padding: 12px 14px; font-weight: 900; text-align:right;">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($types as $type)
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 12px 14px; font-weight: 800; color:#0f172a;">{{ $type->name }}</td>
                                        <td style="padding: 12px 14px; text-align:right;">
                                            <input
                                                type="number"
                                                name="type_prices[{{ $type->id }}]"
                                                value="{{ old('type_prices.'.$type->id, $typePrices[$type->id] ?? '') }}"
                                                step="0.01"
                                                min="0.01"
                                                placeholder="Input Price"
                                                style="width: 160px; max-width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px; text-align:right;"
                                            >
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap: 10px; margin-top: 20px;">
                    <a href="{{ route('municipalities.index') }}" class="btn btn-outline" style="display:inline-flex; align-items:center; gap:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-member-layout>
